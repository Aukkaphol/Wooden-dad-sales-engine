<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMaterialController extends Controller
{
    public function index(): View
    {
        return view('admin.materials.index', [
            'materials' => Material::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.materials.create', ['material' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Material::create($this->validatedMaterial($request));

        return redirect()->route('admin.materials.index')->with('success', 'สร้างวัสดุเรียบร้อยแล้ว');
    }

    public function edit(Material $material): View
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $material->update($this->validatedMaterial($request));

        return redirect()->route('admin.materials.index')->with('success', 'อัปเดตวัสดุเรียบร้อยแล้ว');
    }

    private function validatedMaterial(Request $request): array
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['low_stock_level'] = $validated['minimum_stock'] ?? 0;
        $validated['unit_cost'] = $validated['cost_price'] ?? 0;

        return $validated;
    }
}
