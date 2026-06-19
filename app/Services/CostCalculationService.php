<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Collection;

class CostCalculationService
{
    public function productCost(Product $product, float $quantity = 1): array
    {
        $product->loadMissing('bomItems.material');

        $materialCost = $product->bomItems->sum(
            fn ($bomItem): float => (float) $bomItem->quantity * (float) $bomItem->material->unit_cost
        );
        $laborCost = (float) $product->labor_cost;
        $hardwareCost = (float) $product->hardware_cost;
        $finishingCost = (float) $product->finishing_cost;
        $otherCost = (float) $product->other_cost;
        $sellingPrice = (float) $product->selling_price;
        $unitProductionCost = $materialCost + $laborCost + $hardwareCost + $finishingCost + $otherCost;
        $profitAmount = $sellingPrice - $unitProductionCost;
        $profitPercent = $sellingPrice > 0 ? ($profitAmount / $sellingPrice) * 100 : 0;

        $product->forceFill([
            'material_cost' => round($materialCost, 2),
            'total_cost' => round($unitProductionCost, 2),
            'profit_amount' => round($profitAmount, 2),
            'profit_percent' => round($profitPercent, 2),
        ])->save();

        return [
            'product' => $product,
            'quantity' => $quantity,
            'selling_price' => round($sellingPrice * $quantity, 2),
            'material_cost' => round($materialCost * $quantity, 2),
            'labor_cost' => round($laborCost * $quantity, 2),
            'hardware_cost' => round($hardwareCost * $quantity, 2),
            'finishing_cost' => round($finishingCost * $quantity, 2),
            'other_cost' => round($otherCost * $quantity, 2),
            'unit_production_cost' => round($unitProductionCost, 2),
            'production_cost' => round($unitProductionCost * $quantity, 2),
            'profit_amount' => round($profitAmount * $quantity, 2),
            'profit_percent' => round($profitPercent, 2),
        ];
    }

    public function quotationItemCost(QuotationItem $item): array
    {
        $product = $this->findProductByName($item->product_name);
        $sellingPrice = (float) $item->subtotal;

        if (! $product) {
            return [
                'item' => $item,
                'product' => null,
                'selling_price' => $sellingPrice,
                'material_cost' => 0.0,
                'labor_cost' => 0.0,
                'hardware_cost' => 0.0,
                'finishing_cost' => 0.0,
                'other_cost' => 0.0,
                'production_cost' => 0.0,
                'gross_profit' => $sellingPrice,
                'profit_percent' => $sellingPrice > 0 ? 100.0 : 0.0,
            ];
        }

        $cost = $this->productCost($product, (float) $item->quantity);
        $grossProfit = $sellingPrice - $cost['production_cost'];

        return [
            ...$cost,
            'item' => $item,
            'selling_price' => $sellingPrice,
            'gross_profit' => round($grossProfit, 2),
            'profit_percent' => $sellingPrice > 0 ? round(($grossProfit / $sellingPrice) * 100, 2) : 0.0,
        ];
    }

    public function quotationCost(Quotation $quotation): array
    {
        $quotation->loadMissing('items');

        $lines = $quotation->items->map(fn (QuotationItem $item): array => $this->quotationItemCost($item));
        $sellingPrice = $lines->sum('selling_price');
        $productionCost = $lines->sum('production_cost');
        $grossProfit = $sellingPrice - $productionCost;

        return [
            'lines' => $lines,
            'selling_price' => round($sellingPrice, 2),
            'material_cost' => round($lines->sum('material_cost'), 2),
            'labor_cost' => round($lines->sum('labor_cost'), 2),
            'hardware_cost' => round($lines->sum('hardware_cost'), 2),
            'finishing_cost' => round($lines->sum('finishing_cost'), 2),
            'other_cost' => round($lines->sum('other_cost'), 2),
            'production_cost' => round($productionCost, 2),
            'gross_profit' => round($grossProfit, 2),
            'profit_percent' => $sellingPrice > 0 ? round(($grossProfit / $sellingPrice) * 100, 2) : 0.0,
        ];
    }

    public function productionOrderCost(ProductionOrder $productionOrder): array
    {
        $productionOrder->loadMissing('quotation.items');

        $quotationCost = $this->quotationCost($productionOrder->quotation);
        $deliveryCost = (float) $productionOrder->delivery_cost;
        $totalCost = $quotationCost['production_cost'] + $deliveryCost;
        $expectedProfit = $quotationCost['selling_price'] - $totalCost;
        $grossMargin = $quotationCost['selling_price'] > 0
            ? ($expectedProfit / $quotationCost['selling_price']) * 100
            : 0.0;

        $productionOrder->forceFill([
            'material_cost' => $quotationCost['material_cost'],
            'labor_cost' => $quotationCost['labor_cost'],
            'total_cost' => round($totalCost, 2),
            'gross_margin' => round($grossMargin, 2),
        ])->save();

        return [
            ...$quotationCost,
            'delivery_cost' => round($deliveryCost, 2),
            'total_cost' => round($totalCost, 2),
            'expected_profit' => round($expectedProfit, 2),
            'gross_margin' => round($grossMargin, 2),
        ];
    }

    public function profitabilityByProduct(): Collection
    {
        return QuotationItem::query()
            ->with('quotation')
            ->get()
            ->map(fn (QuotationItem $item): array => $this->quotationItemCost($item))
            ->filter(fn (array $line): bool => $line['product'] !== null)
            ->groupBy(fn (array $line): string => $line['product']->name)
            ->map(function (Collection $lines, string $productName): array {
                $sellingPrice = $lines->sum('selling_price');
                $productionCost = $lines->sum('production_cost');
                $grossProfit = $sellingPrice - $productionCost;

                return [
                    'sku' => $lines->first()['product']->sku,
                    'product_name' => $productName,
                    'quantity_sold' => round($lines->sum(fn (array $line): float => (float) $line['item']->quantity), 2),
                    'selling_price' => round($sellingPrice, 2),
                    'revenue' => round($sellingPrice, 2),
                    'production_cost' => round($productionCost, 2),
                    'gross_profit' => round($grossProfit, 2),
                    'profit' => round($grossProfit, 2),
                    'profit_percent' => $sellingPrice > 0 ? round(($grossProfit / $sellingPrice) * 100, 2) : 0.0,
                ];
            })
            ->values();
    }

    public function refreshProducts(): Collection
    {
        return Product::query()
            ->with('bomItems.material')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => $this->productCost($product));
    }

    private function findProductByName(string $productName): ?Product
    {
        return Product::query()
            ->with('bomItems.material')
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($productName))])
            ->first();
    }
}
