<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPurchaseController extends Controller
{
    public function index(PurchaseService $purchaseService): View
    {
        $purchaseByMonth = PurchaseOrder::query()
            ->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, SUM(total_cost) as value')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.purchase.index', [
            'metrics' => [
                'open_pr' => PurchaseRequisition::whereIn('status', ['draft', 'waiting_approval', 'approved'])->count(),
                'open_po' => PurchaseOrder::whereIn('status', ['draft', 'sent', 'partial_received'])->count(),
                'waiting_receipts' => PurchaseOrder::whereIn('status', ['sent', 'partial_received'])->count(),
                'low_stock' => $purchaseService->lowStockSuggestions()->count(),
                'purchase_value_this_month' => PurchaseOrder::whereYear('order_date', now()->year)->whereMonth('order_date', now()->month)->sum('total_cost'),
                'supplier_count' => Supplier::count(),
            ],
            'purchaseByMonth' => $purchaseByMonth,
            'lowStockSuggestions' => $purchaseService->lowStockSuggestions(),
            'purchaseRequisitions' => PurchaseRequisition::with('items.material')->latest()->limit(10)->get(),
            'purchaseOrders' => PurchaseOrder::with(['supplier', 'items.material'])->latest()->limit(10)->get(),
            'receipts' => GoodsReceipt::with(['purchaseOrder', 'material'])->latest()->limit(10)->get(),
        ]);
    }

    public function createPr(): View
    {
        return view('admin.purchase.pr-form', [
            'materials' => Material::orderBy('name')->get(),
            'prNumber' => app(PurchaseService::class)->nextPrNumber(),
        ]);
    }

    public function storePr(Request $request, PurchaseService $purchaseService): RedirectResponse
    {
        $validated = $request->validate([
            'request_date' => ['required', 'date'],
            'requested_by' => ['required', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:draft,waiting_approval,approved,rejected'],
            'material_id' => ['required', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
        ]);

        $material = Material::findOrFail($validated['material_id']);
        $pr = DB::transaction(function () use ($validated, $purchaseService, $material): PurchaseRequisition {
            $pr = PurchaseRequisition::create([
                'pr_number' => $purchaseService->nextPrNumber(),
                'request_date' => $validated['request_date'],
                'requested_by' => $validated['requested_by'],
                'reason' => $validated['reason'] ?? null,
                'status' => $validated['status'],
            ]);
            $pr->items()->create([
                'material_id' => $material->id,
                'quantity' => $validated['quantity'],
                'unit' => $material->unit,
                'reason' => $validated['reason'] ?? null,
            ]);

            return $pr;
        });

        return redirect()->route('admin.purchase.pr.show', $pr)->with('success', 'สร้างใบขอซื้อเรียบร้อยแล้ว');
    }

    public function showPr(PurchaseRequisition $purchaseRequisition): View
    {
        $purchaseRequisition->load(['items.material', 'productionOrder', 'productionOrder.lead']);

        return view('admin.purchase.pr-show', ['pr' => $purchaseRequisition]);
    }

    public function updatePrStatus(Request $request, PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:'.implode(',', array_keys(PurchaseRequisition::STATUSES))]]);
        $purchaseRequisition->update($validated);

        return back()->with('success', 'อัปเดตสถานะใบขอซื้อเรียบร้อยแล้ว');
    }

    public function createPo(): View
    {
        return view('admin.purchase.po-form', [
            'materials' => Material::orderBy('name')->get(),
            'suppliers' => Supplier::where('is_active', true)->orderBy('supplier_name')->get(),
            'approvedPrs' => PurchaseRequisition::where('status', 'approved')->orderByDesc('request_date')->get(),
            'poNumber' => app(PurchaseService::class)->nextPoNumber(),
        ]);
    }

    public function storePo(Request $request, PurchaseService $purchaseService): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_requisition_id' => ['nullable', 'exists:purchase_requisitions,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:draft,sent'],
            'material_id' => ['required', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $material = Material::findOrFail($validated['material_id']);
        $po = DB::transaction(function () use ($validated, $purchaseService, $material): PurchaseOrder {
            $total = round((float) $validated['quantity'] * (float) $validated['unit_cost'], 2);
            $po = PurchaseOrder::create([
                'po_number' => $purchaseService->nextPoNumber(),
                'supplier_id' => $validated['supplier_id'],
                'purchase_requisition_id' => $validated['purchase_requisition_id'] ?? null,
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'total_cost' => $total,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);
            $po->items()->create([
                'material_id' => $material->id,
                'quantity' => $validated['quantity'],
                'unit' => $material->unit,
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $total,
            ]);

            if ($po->purchase_requisition_id) {
                PurchaseRequisition::whereKey($po->purchase_requisition_id)->update(['status' => 'converted_to_po']);
            }

            return $po;
        });

        return redirect()->route('admin.purchase.po.show', $po)->with('success', 'สร้างใบสั่งซื้อเรียบร้อยแล้ว');
    }

    public function showPo(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'purchaseRequisition', 'items.material', 'receipts.material']);

        return view('admin.purchase.po-show', ['po' => $purchaseOrder]);
    }

    public function receive(Request $request, PurchaseOrderItem $purchaseOrderItem, PurchaseService $purchaseService): RedirectResponse
    {
        $validated = $request->validate([
            'receive_date' => ['required', 'date'],
            'received_quantity' => ['required', 'numeric', 'min:0.001'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $purchaseService->receiveItem($purchaseOrderItem, (float) $validated['received_quantity'], $validated['receive_date'], $validated['notes'] ?? null);

        return back()->with('success', 'รับวัตถุดิบเข้าคลังเรียบร้อยแล้ว');
    }

    public function autoPrFromLowStock(Material $material, PurchaseService $purchaseService): RedirectResponse
    {
        $suggested = max(0, (float) $material->low_stock_level - (float) $material->current_stock);
        if ($suggested <= 0) {
            return back()->with('success', 'สต็อกวัสดุยังไม่ต่ำกว่าขั้นต่ำ');
        }

        $pr = PurchaseRequisition::create([
            'pr_number' => $purchaseService->nextPrNumber(),
            'request_date' => today(),
            'requested_by' => 'ระบบคลังวัสดุ',
            'reason' => 'สร้างจากรายการวัตถุดิบต่ำกว่าขั้นต่ำ',
            'status' => 'waiting_approval',
        ]);
        $pr->items()->create([
            'material_id' => $material->id,
            'quantity' => $suggested,
            'unit' => $material->unit,
            'reason' => 'สต็อกปัจจุบันต่ำกว่าระดับขั้นต่ำ',
        ]);

        return redirect()->route('admin.purchase.pr.show', $pr)->with('success', 'สร้าง PR อัตโนมัติจาก Low Stock แล้ว');
    }

    public function autoPrFromProduction(ProductionOrder $productionOrder, PurchaseService $purchaseService): RedirectResponse
    {
        $pr = $purchaseService->createPrFromProduction($productionOrder);

        if (! $pr) {
            return back()->with('success', 'วัตถุดิบเพียงพอสำหรับงานผลิตนี้');
        }

        return redirect()->route('admin.purchase.pr.show', $pr)->with('success', 'สร้าง PR อัตโนมัติจากวัตถุดิบไม่เพียงพอแล้ว');
    }

    public function report(string $type, string $format): StreamedResponse|View
    {
        $rows = $this->reportRows($type);
        $title = [
            'purchase-summary' => 'สรุปการจัดซื้อ',
            'supplier-summary' => 'สรุปยอดซื้อตามผู้จำหน่าย',
            'material-consumption' => 'รายงานการใช้วัสดุ',
            'low-stock' => 'รายงานวัตถุดิบใกล้หมด',
            'outstanding-po' => 'รายงานใบสั่งซื้อค้างรับ',
        ][$type] ?? 'รายงานจัดซื้อ';

        if ($format === 'pdf') {
            return view('admin.purchase.report', compact('rows', 'title'));
        }

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $type.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportRows(string $type): array
    {
        return match ($type) {
            'supplier-summary' => Supplier::with('purchaseOrders')->get()
                ->map(fn (Supplier $supplier): array => [$supplier->supplier_code, $supplier->supplier_name, $supplier->purchaseOrders->count(), number_format($supplier->purchaseOrders->sum('total_cost'), 2)])
                ->prepend(['รหัส', 'ผู้จำหน่าย', 'จำนวน PO', 'ยอดซื้อ'])->all(),
            'material-consumption' => DB::table('stock_transactions')->join('materials', 'materials.id', '=', 'stock_transactions.material_id')
                ->where('stock_transactions.type', 'consume')->selectRaw('materials.name, SUM(ABS(quantity)) qty, SUM(ABS(quantity)*stock_transactions.unit_cost) total')->groupBy('materials.name')->get()
                ->map(fn ($row): array => [$row->name, number_format((float) $row->qty, 3), number_format((float) $row->total, 2)])
                ->prepend(['วัสดุ', 'จำนวนใช้', 'มูลค่า'])->all(),
            'low-stock' => app(PurchaseService::class)->lowStockSuggestions()
                ->map(fn (array $row): array => [$row['material']->name, $row['current'], $row['minimum'], $row['suggested_quantity']])
                ->prepend(['วัสดุ', 'คงเหลือ', 'ขั้นต่ำ', 'แนะนำซื้อ'])->all(),
            'outstanding-po' => PurchaseOrder::with(['supplier', 'items.material'])->whereIn('status', ['sent', 'partial_received'])->get()
                ->flatMap(fn (PurchaseOrder $po) => $po->items->map(fn (PurchaseOrderItem $item): array => [$po->po_number, $po->supplier->supplier_name, $item->material->name, (float) $item->quantity, (float) $item->received_quantity, max(0, (float) $item->quantity - (float) $item->received_quantity)]))
                ->prepend(['PO', 'ผู้จำหน่าย', 'วัสดุ', 'สั่งซื้อ', 'รับแล้ว', 'ค้างรับ'])->all(),
            default => PurchaseOrder::with('supplier')->latest()->get()
                ->map(fn (PurchaseOrder $po): array => [$po->po_number, $po->supplier->supplier_name, $po->order_date->format('d/m/Y'), $po->status_label, number_format((float) $po->total_cost, 2)])
                ->prepend(['PO', 'ผู้จำหน่าย', 'วันที่สั่งซื้อ', 'สถานะ', 'ยอดรวม'])->all(),
        };
    }
}
