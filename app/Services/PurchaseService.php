<?php

namespace App\Services;

use App\Models\GoodsReceipt;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(private readonly InventoryService $inventoryService)
    {
    }

    public function nextPrNumber(): string
    {
        return $this->nextNumber(PurchaseRequisition::class, 'pr_number', 'PR');
    }

    public function nextPoNumber(): string
    {
        return $this->nextNumber(PurchaseOrder::class, 'po_number', 'PO-SUP');
    }

    public function nextPurchaseRequestNumber(): string
    {
        return $this->nextNumber(PurchaseRequest::class, 'pr_no', 'PR');
    }

    public function lowStockSuggestions(): Collection
    {
        return Material::query()
            ->orderBy('name')
            ->get()
            ->filter(fn (Material $material): bool => (float) $material->current_stock < $material->minimum_stock_value)
            ->map(fn (Material $material): array => [
                'material' => $material,
                'current' => (float) $material->current_stock,
                'minimum' => $material->minimum_stock_value,
                'suggested_quantity' => max(0, $material->minimum_stock_value - (float) $material->current_stock),
            ])
            ->values();
    }

    public function shortageForProduction(ProductionOrder $productionOrder): Collection
    {
        return $this->inventoryService->shortagesForProduction($productionOrder)
            ->map(fn (array $row): array => [
                'material' => $row['material'],
                'required' => $row['required_qty'],
                'available' => $row['current_stock'],
                'shortage' => $row['shortage'],
            ]);
    }

    public function createPurchaseRequest(Material $material, float $quantity, string $reason, ?ProductionOrder $productionOrder = null, string $status = 'pending'): PurchaseRequest
    {
        return DB::transaction(function () use ($material, $quantity, $reason, $productionOrder, $status): PurchaseRequest {
            $existing = PurchaseRequest::query()
                ->where('material_id', $material->id)
                ->where('production_order_id', $productionOrder?->id)
                ->whereIn('status', ['draft', 'pending', 'approved'])
                ->first();

            if ($existing) {
                if ((float) $existing->requested_qty < $quantity) {
                    $existing->update(['requested_qty' => $quantity, 'reason' => $reason]);
                }

                return $existing;
            }

            return PurchaseRequest::create([
                'pr_no' => $this->nextPurchaseRequestNumber(),
                'material_id' => $material->id,
                'production_order_id' => $productionOrder?->id,
                'requested_qty' => $quantity,
                'reason' => $reason,
                'status' => $status,
            ]);
        });
    }

    public function createPurchaseRequestsForShortages(ProductionOrder $productionOrder, Collection $shortages): Collection
    {
        return $shortages->map(function (array $row) use ($productionOrder): PurchaseRequest {
            return $this->createPurchaseRequest(
                $row['material'],
                (float) ($row['shortage'] ?? 0),
                'วัสดุไม่เพียงพอสำหรับใบสั่งผลิต '.$productionOrder->production_order_number,
                $productionOrder
            );
        });
    }

    public function createPrFromProduction(ProductionOrder $productionOrder): ?PurchaseRequisition
    {
        $shortages = $this->shortageForProduction($productionOrder);

        if ($shortages->isEmpty()) {
            return null;
        }

        $this->createPurchaseRequestsForShortages($productionOrder, $shortages);

        return DB::transaction(function () use ($productionOrder, $shortages): PurchaseRequisition {
            $pr = PurchaseRequisition::create([
                'pr_number' => $this->nextPrNumber(),
                'request_date' => today(),
                'requested_by' => 'ฝ่ายผลิต',
                'reason' => 'วัสดุไม่เพียงพอสำหรับใบสั่งผลิต '.$productionOrder->production_order_number,
                'status' => 'pending',
                'production_order_id' => $productionOrder->id,
            ]);

            foreach ($shortages as $row) {
                $pr->items()->create([
                    'material_id' => $row['material']->id,
                    'quantity' => $row['shortage'],
                    'unit' => $row['material']->unit,
                    'reason' => 'วัสดุไม่เพียงพอสำหรับงานผลิต',
                ]);
            }

            return $pr;
        });
    }

    public function receiveItem(PurchaseOrderItem $item, float $quantity, string $receiveDate, ?string $notes = null): GoodsReceipt
    {
        return DB::transaction(function () use ($item, $quantity, $receiveDate, $notes): GoodsReceipt {
            $item = PurchaseOrderItem::query()->with(['purchaseOrder', 'material'])->lockForUpdate()->findOrFail($item->id);
            $remainingBefore = max(0, (float) $item->quantity - (float) $item->received_quantity);
            $receivedQuantity = min($quantity, $remainingBefore);
            $remainingAfter = max(0, $remainingBefore - $receivedQuantity);

            $receipt = GoodsReceipt::create([
                'purchase_order_id' => $item->purchase_order_id,
                'purchase_order_item_id' => $item->id,
                'material_id' => $item->material_id,
                'receive_date' => $receiveDate,
                'ordered_quantity' => $item->quantity,
                'received_quantity' => $receivedQuantity,
                'remaining_quantity' => $remainingAfter,
                'unit_cost' => $item->unit_cost,
                'notes' => $notes,
            ]);

            $beforeStock = (float) $item->material->current_stock;
            $afterStock = $beforeStock + $receivedQuantity;

            $item->increment('received_quantity', $receivedQuantity);
            $item->material->update([
                'current_stock' => $afterStock,
                'unit_cost' => $item->unit_cost,
                'cost_price' => $item->unit_cost,
            ]);
            $item->material->transactions()->create([
                'type' => 'receive',
                'quantity' => $receivedQuantity,
                'unit_cost' => $item->unit_cost,
                'notes' => 'รับเข้าจาก '.$item->purchaseOrder->po_number,
            ]);
            $item->material->movements()->create([
                'type' => 'receive',
                'reference_type' => PurchaseOrder::class,
                'reference_id' => $item->purchase_order_id,
                'qty' => $receivedQuantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'remark' => 'รับเข้าจาก '.$item->purchaseOrder->po_number,
            ]);

            $purchaseOrder = $item->purchaseOrder->fresh('items');
            $allReceived = $purchaseOrder->items->every(fn (PurchaseOrderItem $poItem): bool => (float) $poItem->received_quantity >= (float) $poItem->quantity);
            $anyReceived = $purchaseOrder->items->sum(fn (PurchaseOrderItem $poItem): float => (float) $poItem->received_quantity) > 0;
            $purchaseOrder->update(['status' => $allReceived ? 'completed' : ($anyReceived ? 'partial_received' : $purchaseOrder->status)]);

            return $receipt;
        });
    }

    private function nextNumber(string $modelClass, string $column, string $prefix): string
    {
        $base = $prefix.'-'.now()->format('Ym').'-';
        $latest = $modelClass::query()
            ->where($column, 'like', $base.'%')
            ->orderByDesc($column)
            ->value($column);
        $next = $latest ? ((int) substr((string) $latest, -4)) + 1 : 1;

        return $base.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
