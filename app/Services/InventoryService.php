<?php

namespace App\Services;

use App\Models\BomItem;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function reserveForProduction(ProductionOrder $productionOrder): void
    {
        if ($productionOrder->materials_reserved_at) {
            return;
        }

        DB::transaction(function () use ($productionOrder): void {
            foreach ($this->requiredMaterials($productionOrder) as $requirement) {
                /** @var Material $material */
                $material = Material::lockForUpdate()->find($requirement['material']->id);
                $quantity = $requirement['quantity'];

                $material->increment('reserved_stock', $quantity);
                $material->transactions()->create([
                    'production_order_id' => $productionOrder->id,
                    'type' => 'reserve',
                    'quantity' => $quantity,
                    'unit_cost' => $material->unit_cost,
                    'notes' => 'Reserved for '.$productionOrder->production_order_number,
                ]);
            }

            $productionOrder->update(['materials_reserved_at' => now()]);
        });
    }

    public function consumeForProduction(ProductionOrder $productionOrder): void
    {
        if ($productionOrder->materials_consumed_at) {
            return;
        }

        DB::transaction(function () use ($productionOrder): void {
            foreach ($this->requiredMaterials($productionOrder) as $requirement) {
                /** @var Material $material */
                $material = Material::lockForUpdate()->find($requirement['material']->id);
                $quantity = $requirement['quantity'];

                $material->decrement('current_stock', $quantity);
                $material->decrement('reserved_stock', min((float) $material->reserved_stock, $quantity));
                $material->transactions()->create([
                    'production_order_id' => $productionOrder->id,
                    'type' => 'consume',
                    'quantity' => -1 * $quantity,
                    'unit_cost' => $material->unit_cost,
                    'notes' => 'Consumed for '.$productionOrder->production_order_number,
                ]);
            }

            $productionOrder->update(['materials_consumed_at' => now()]);
        });
    }

    public function requiredMaterials(ProductionOrder $productionOrder): array
    {
        $requirements = [];
        $productionOrder->loadMissing('quotation.items');

        foreach ($productionOrder->quotation->items as $quotationItem) {
            $product = Product::with('bomItems.material')
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($quotationItem->product_name))])
                ->first();

            if (! $product) {
                continue;
            }

            foreach ($product->bomItems as $bomItem) {
                $key = $bomItem->material_id;
                $requirements[$key] ??= [
                    'material' => $bomItem->material,
                    'quantity' => 0,
                ];
                $requirements[$key]['quantity'] += (float) $bomItem->quantity * (float) $quotationItem->quantity;
            }
        }

        return array_values($requirements);
    }
}
