<?php

namespace App\Services;

use App\Models\GoodsReceipt;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
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

    public function lowStockSuggestions(): Collection
    {
        return Material::query()
            ->orderBy('name')
            ->get()
            ->filter(fn (Material $material): bool => (float) $material->current_stock < (float) $material->low_stock_level)
            ->map(fn (Material $material): array => [
                'material' => $material,
                'current' => (float) $material->current_stock,
                'minimum' => (float) $material->low_stock_level,
                'suggested_quantity' => max(0, (float) $material->low_stock_level - (float) $material->current_stock),
            ])
            ->values();
    }

    public function shortageForProduction(ProductionOrder $productionOrder): Collection
    {
        return collect($this->inventoryService->requiredMaterials($productionOrder))
            ->map(function (array $requirement): array {
                $material = $requirement['material'];
                $available = (float) $material->current_stock - (float) $material->reserved_stock;
                $required = (float) $requirement['quantity'];

                return [
                    'material' => $material,
                    'required' => $required,
                    'available' => $available,
                    'shortage' => max(0, $required - $available),
                ];
            })
            ->filter(fn (array $row): bool => $row['shortage'] > 0)
            ->values();
    }

    public function createPrFromProduction(ProductionOrder $productionOrder): ?PurchaseRequisition
    {
        $shortages = $this->shortageForProduction($productionOrder);

        if ($shortages->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($productionOrder, $shortages): PurchaseRequisition {
            $pr = PurchaseRequisition::create([
                'pr_number' => $this->nextPrNumber(),
                'request_date' => today(),
                'requested_by' => 'ฝ่ายผลิต',
                'reason' => 'สร้างอัตโนมัติจากวัสดุไม่เพียงพอสำหรับ '.$productionOrder->production_order_number,
                'status' => 'waiting_approval',
                'production_order_id' => $productionOrder->id,
            ]);

            foreach ($shortages as $row) {
                $pr->items()->create([
                    'material_id' => $row['material']->id,
                    'quantity' => $row['shortage'],
                    'unit' => $row['material']->unit,
                    'reason' => 'วัตถุดิบไม่เพียงพอสำหรับงานผลิต',
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

            $item->increment('received_quantity', $receivedQuantity);
            $item->material->increment('current_stock', $receivedQuantity);
            $item->material->update(['unit_cost' => $item->unit_cost]);
            $item->material->transactions()->create([
                'type' => 'receive',
                'quantity' => $receivedQuantity,
                'unit_cost' => $item->unit_cost,
                'notes' => 'รับเข้าจาก '.$item->purchaseOrder->po_number,
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
