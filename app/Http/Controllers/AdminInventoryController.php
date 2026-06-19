<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\Material;
use App\Models\StockTransaction;
use App\Services\CostCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    public function index(CostCalculationService $costCalculationService): View
    {
        $materials = Material::orderBy('name')->get();
        $usage = StockTransaction::query()
            ->with('material')
            ->where('type', 'consume')
            ->selectRaw('material_id, SUM(ABS(quantity)) as total_quantity, SUM(ABS(quantity) * unit_cost) as total_cost')
            ->groupBy('material_id')
            ->orderByDesc('total_cost')
            ->get();
        $profitability = $costCalculationService->profitabilityByProduct();

        return view('admin.inventory.index', [
            'materials' => $materials,
            'lowStockMaterials' => $materials->filter(fn (Material $material) => (float) $material->current_stock <= (float) $material->low_stock_level),
            'usage' => $usage,
            'materialCost' => $materials->sum(fn (Material $material) => (float) $material->current_stock * (float) $material->unit_cost),
            'bomItems' => BomItem::with(['product', 'material'])->get(),
            'productCosts' => $costCalculationService->refreshProducts(),
            'topProfitableProducts' => $profitability->sortByDesc('gross_profit')->take(5)->values(),
            'topLowMarginProducts' => $profitability->sortBy('profit_percent')->take(5)->values(),
            'transactions' => StockTransaction::with(['material', 'productionOrder'])->latest()->limit(20)->get(),
            'transactionTypes' => [
                'receive' => 'รับวัสดุเข้าคลัง',
                'adjust' => 'ปรับยอดสต็อก',
                'consume' => 'ตัดใช้วัสดุ',
            ],
        ]);
    }

    public function storeTransaction(Request $request, Material $material): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:receive,adjust,consume'],
            'quantity' => ['required', 'numeric', 'not_in:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $quantity = (float) $validated['quantity'];
        if ($validated['type'] !== 'adjust' && $quantity < 0) {
            return back()
                ->withErrors(['quantity' => 'จำนวนรับเข้าและตัดใช้ต้องมากกว่า 0'])
                ->withInput();
        }

        $signedQuantity = match ($validated['type']) {
            'consume' => -abs($quantity),
            'adjust' => $quantity,
            default => abs($quantity),
        };
        $unitCost = $validated['unit_cost'] ?? $material->unit_cost;

        $material->increment('current_stock', $signedQuantity);
        $material->update(['unit_cost' => $unitCost]);
        $material->transactions()->create([
            'type' => $validated['type'],
            'quantity' => $signedQuantity,
            'unit_cost' => $unitCost,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'อัปเดตสต็อกวัสดุเรียบร้อยแล้ว');
    }
}
