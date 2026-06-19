<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSupplierController extends Controller
{
    public function index(): View
    {
        return view('admin.suppliers.index', [
            'suppliers' => Supplier::query()->withCount('purchaseOrders')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.form', [
            'supplier' => new Supplier(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supplier = Supplier::create($this->validated($request));

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'บันทึกผู้จำหน่ายเรียบร้อยแล้ว');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchaseOrders.items.material']);

        return view('admin.suppliers.show', ['supplier' => $supplier]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.form', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request, $supplier));

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'อัปเดตผู้จำหน่ายเรียบร้อยแล้ว');
    }

    private function validated(Request $request, ?Supplier $supplier = null): array
    {
        $supplierId = $supplier?->id ?? 'NULL';

        return $request->validate([
            'supplier_code' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_code,'.$supplierId],
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'line_id' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
