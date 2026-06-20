<?php

namespace App\Services;

use App\Models\Material;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function reserveForProduction(ProductionOrder $productionOrder): void
    {
        // Kept for backward compatibility. Sprint BOM flow deducts on production start instead.
    }

    public function consumeForProduction(ProductionOrder $productionOrder): void
    {
        // Kept for backward compatibility. Stock is consumed when status moves waiting -> cutting.
    }

    public function requiredMaterials(ProductionOrder $productionOrder): array
    {
        return $this->materialRequirements($productionOrder)->map(fn (array $row): array => [
            'material' => $row['material'],
            'quantity' => $row['required_qty'],
        ])->all();
    }

    public function materialRequirements(ProductionOrder $productionOrder): Collection
    {
        $requirements = collect();
        $productionOrder->loadMissing(['quotation.items', 'items']);
        $sourceItems = $productionOrder->items->isNotEmpty() ? $productionOrder->items : $productionOrder->quotation->items;

        foreach ($sourceItems as $item) {
            $itemName = $item->item_name ?? $item->display_name ?? $item->product_name;
            $orderQty = (float) ($item->qty ?? $item->display_quantity ?? $item->quantity ?? 1);
            $product = Product::query()
                ->with('bomItems.material')
                ->whereRaw('LOWER(name) = ?', [strtolower(trim((string) $itemName))])
                ->orWhereRaw('LOWER(sku) = ?', [strtolower(trim((string) $itemName))])
                ->first();

            if (! $product) {
                continue;
            }

            foreach ($product->bomItems as $bomItem) {
                $material = $bomItem->material;
                if (! $material) {
                    continue;
                }

                $requiredQty = (float) $bomItem->required_quantity * $orderQty * (1 + ((float) $bomItem->waste_percent / 100));
                $key = $material->id;
                $existing = $requirements->get($key, [
                    'material' => $material,
                    'required_qty' => 0.0,
                    'current_stock' => (float) $material->current_stock,
                    'shortage' => 0.0,
                    'status' => 'พร้อมใช้',
                ]);
                $existing['required_qty'] += $requiredQty;
                $existing['shortage'] = max(0, $existing['required_qty'] - (float) $material->current_stock);
                $existing['status'] = $existing['shortage'] > 0 ? 'ไม่พอ' : 'พร้อมใช้';
                $requirements->put($key, $existing);
            }
        }

        return $requirements->values();
    }

    public function shortagesForProduction(ProductionOrder $productionOrder): Collection
    {
        return $this->materialRequirements($productionOrder)
            ->filter(fn (array $row): bool => (float) $row['shortage'] > 0)
            ->values();
    }

    public function deductForProductionStart(ProductionOrder $productionOrder): Collection
    {
        if ($productionOrder->materials_consumed_at) {
            return collect();
        }

        return DB::transaction(function () use ($productionOrder): Collection {
            $requirements = $this->materialRequirements($productionOrder);
            $materialIds = $requirements->pluck('material.id')->all();
            $materials = Material::query()->whereIn('id', $materialIds)->lockForUpdate()->get()->keyBy('id');

            $shortages = $requirements->map(function (array $row) use ($materials): array {
                $material = $materials->get($row['material']->id);
                $row['material'] = $material;
                $row['current_stock'] = (float) $material->current_stock;
                $row['shortage'] = max(0, (float) $row['required_qty'] - (float) $material->current_stock);
                $row['status'] = $row['shortage'] > 0 ? 'ไม่พอ' : 'พร้อมใช้';

                return $row;
            })->filter(fn (array $row): bool => $row['shortage'] > 0)->values();

            if ($shortages->isNotEmpty()) {
                return $shortages;
            }

            foreach ($requirements as $row) {
                /** @var Material $material */
                $material = $materials->get($row['material']->id);
                $beforeStock = (float) $material->current_stock;
                $qty = (float) $row['required_qty'];
                $afterStock = $beforeStock - $qty;

                $material->update([
                    'current_stock' => $afterStock,
                    'cost_price' => $material->cost_price ?: $material->unit_cost,
                    'unit_cost' => $material->unit_cost ?: $material->cost_price,
                ]);

                StockMovement::create([
                    'material_id' => $material->id,
                    'type' => 'consume',
                    'reference_type' => ProductionOrder::class,
                    'reference_id' => $productionOrder->id,
                    'qty' => -1 * $qty,
                    'before_stock' => $beforeStock,
                    'after_stock' => $afterStock,
                    'remark' => 'ตัดสต๊อกสำหรับใบสั่งผลิต '.$productionOrder->production_order_number,
                ]);

                $material->transactions()->create([
                    'production_order_id' => $productionOrder->id,
                    'type' => 'consume',
                    'quantity' => -1 * $qty,
                    'unit_cost' => $material->cost_price_value,
                    'notes' => 'Consumed for '.$productionOrder->production_order_number,
                ]);
            }

            $productionOrder->update(['materials_consumed_at' => now()]);

            return collect();
        });
    }
}
