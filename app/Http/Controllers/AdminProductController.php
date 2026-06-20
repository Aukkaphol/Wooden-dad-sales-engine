<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use App\Models\Material;
use App\Models\Product;
use App\Services\CostCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(CostCalculationService $costCalculationService): View
    {
        return view('admin.products.index', [
            'products' => Product::withCount('bomItems')->orderBy('name')->get(),
            'productCosts' => $costCalculationService->refreshProducts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', ['product' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validatedProduct($request));

        return redirect()->route('admin.products.index')->with('success', 'สร้างสินค้าเรียบร้อยแล้ว');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validatedProduct($request));

        return redirect()->route('admin.products.index')->with('success', 'อัปเดตสินค้าเรียบร้อยแล้ว');
    }

    public function bom(Product $product, CostCalculationService $costCalculationService): View
    {
        $product->load('bomItems.material');

        return view('admin.products.bom', [
            'product' => $product,
            'materials' => Material::orderBy('name')->get(),
            'productCost' => $costCalculationService->productCost($product),
        ]);
    }

    public function storeBomItem(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
            'qty_required' => ['required', 'numeric', 'min:0.001'],
            'waste_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        BomItem::updateOrCreate(
            ['product_id' => $product->id, 'material_id' => $validated['material_id']],
            [
                'qty_required' => $validated['qty_required'],
                'quantity' => $validated['qty_required'],
                'waste_percent' => $validated['waste_percent'] ?? 0,
            ]
        );

        return back()->with('success', 'อัปเดต BOM เรียบร้อยแล้ว');
    }

    public function destroyBomItem(Product $product, BomItem $bomItem): RedirectResponse
    {
        abort_unless($bomItem->product_id === $product->id, 404);
        $bomItem->delete();

        return back()->with('success', 'ลบรายการ BOM เรียบร้อยแล้ว');
    }

    private function validatedProduct(Request $request): array
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'product_image' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['image'] = $validated['product_image'] ?? null;
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }
}
