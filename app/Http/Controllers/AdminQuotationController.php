<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ProductionOrder;
use App\Models\Quotation;
use App\Services\CostCalculationService;
use App\Services\LineNotificationService;
use App\Services\QuotationPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminQuotationController extends Controller
{
    public function index(Request $request): View
    {
        $quotations = Quotation::query()
            ->with(['lead', 'productionOrder'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->string('search'));
                $query->where(function ($inner) use ($search): void {
                    $inner->where('quotation_no', 'like', "%{$search}%")
                        ->orWhere('quotation_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.quotations.index', [
            'quotations' => $quotations,
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function create(Request $request): View
    {
        $lead = $request->filled('lead_id') ? Lead::find($request->integer('lead_id')) : null;

        return view('admin.quotations.create', [
            'lead' => $lead,
            'leads' => $this->leadOptions($lead),
            'statuses' => Quotation::STATUSES,
            'quotation' => null,
        ]);
    }

    public function createFromLead(Lead $lead): View
    {
        return view('admin.quotations.create', [
            'lead' => $lead,
            'leads' => $this->leadOptions($lead),
            'statuses' => Quotation::STATUSES,
            'quotation' => null,
        ]);
    }

    public function store(Request $request, ?Lead $lead = null): RedirectResponse
    {
        $lead = $lead ?: Lead::findOrFail($request->integer('lead_id'));
        $validated = $this->validateQuotation($request);

        $quotation = DB::transaction(function () use ($lead, $validated): Quotation {
            $quotationNo = $this->nextQuotationNumber();
            $totals = $this->calculateTotals($validated);

            $quotation = $lead->quotations()->create([
                'quotation_no' => $quotationNo,
                'quotation_number' => $quotationNo,
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'project_name' => $validated['project_name'] ?? null,
                'status' => $validated['status'],
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'shipping_cost' => $totals['shipping_cost'],
                'deposit_amount' => $totals['deposit_amount'],
                'grand_total' => $totals['grand_total'],
                'valid_until' => $validated['valid_until'] ?? null,
                'remark' => $validated['remark'] ?? null,
                'notes' => $validated['remark'] ?? null,
                'approved_at' => $validated['status'] === 'approved' ? now() : null,
            ]);

            $this->syncItems($quotation, $validated['items']);
            $this->writeLeadNote($quotation, 'สร้างใบเสนอราคา '.$quotation->display_number.' ยอดรวม ฿'.number_format((float) $quotation->grand_total, 2));

            if ($quotation->status === 'approved') {
                $this->ensureProductionOrder($quotation);
            }

            return $quotation;
        });

        app(LineNotificationService::class)->notifyQuotationCreated($quotation->fresh(['lead']));

        if ($quotation->status === 'approved') {
            app(LineNotificationService::class)->notifyQuotationApproved($quotation->fresh(['lead', 'productionOrder']));
        }

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', 'สร้างใบเสนอราคาเรียบร้อยแล้ว');
    }

    public function show(Quotation $quotation, CostCalculationService $costCalculationService): View
    {
        $quotation->load(['lead', 'items', 'productionOrder']);

        return view('admin.quotations.show', [
            'quotation' => $quotation,
            'statuses' => Quotation::STATUSES,
            'costSummary' => $costCalculationService->quotationCost($quotation),
        ]);
    }

    public function edit(Quotation $quotation): View
    {
        $quotation->load(['lead', 'items']);

        return view('admin.quotations.edit', [
            'quotation' => $quotation,
            'lead' => $quotation->lead,
            'leads' => $this->leadOptions($quotation->lead),
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $this->validateQuotation($request);
        $wasApproved = $quotation->status === 'approved';

        DB::transaction(function () use ($quotation, $validated): void {
            $totals = $this->calculateTotals($validated);

            $quotation->update([
                'lead_id' => $validated['lead_id'],
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'province' => $validated['province'],
                'project_name' => $validated['project_name'] ?? null,
                'status' => $validated['status'],
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'shipping_cost' => $totals['shipping_cost'],
                'deposit_amount' => $totals['deposit_amount'],
                'grand_total' => $totals['grand_total'],
                'valid_until' => $validated['valid_until'] ?? null,
                'remark' => $validated['remark'] ?? null,
                'notes' => $validated['remark'] ?? null,
                'approved_at' => $validated['status'] === 'approved' ? ($quotation->approved_at ?: now()) : null,
            ]);

            $this->syncItems($quotation, $validated['items']);
            $this->writeLeadNote($quotation, 'อัปเดตใบเสนอราคา '.$quotation->display_number.' สถานะ '.$quotation->status_label);

            if ($quotation->status === 'approved') {
                $this->ensureProductionOrder($quotation);
            }
        });

        if (! $wasApproved && $quotation->fresh()->status === 'approved') {
            app(LineNotificationService::class)->notifyQuotationApproved($quotation->fresh(['lead', 'productionOrder']));
        }

        return redirect()
            ->route('admin.quotations.show', $quotation)
            ->with('success', 'อัปเดตใบเสนอราคาเรียบร้อยแล้ว');
    }

    public function updateStatus(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Quotation::STATUSES))],
        ]);

        $wasApproved = $quotation->status === 'approved';

        DB::transaction(function () use ($quotation, $validated): void {
            $quotation->update([
                'status' => $validated['status'],
                'approved_at' => $validated['status'] === 'approved' ? ($quotation->approved_at ?: now()) : null,
            ]);

            $this->writeLeadNote($quotation, 'อัปเดตสถานะใบเสนอราคา '.$quotation->display_number.' เป็น '.$quotation->status_label);

            if ($quotation->status === 'approved') {
                $productionOrder = $this->ensureProductionOrder($quotation);
                $this->writeLeadNote($quotation, 'สร้างใบสั่งผลิต '.$productionOrder->production_order_number.' จากใบเสนอราคาที่อนุมัติ');
            }
        });

        if (! $wasApproved && $quotation->fresh()->status === 'approved') {
            app(LineNotificationService::class)->notifyQuotationApproved($quotation->fresh(['lead', 'productionOrder']));
        }

        return back()->with('success', 'อัปเดตสถานะใบเสนอราคาเรียบร้อยแล้ว');
    }

    public function approve(Quotation $quotation): RedirectResponse
    {
        $wasApproved = $quotation->status === 'approved';

        DB::transaction(function () use ($quotation): void {
            $quotation->update([
                'status' => 'approved',
                'approved_at' => $quotation->approved_at ?: now(),
            ]);

            $productionOrder = $this->ensureProductionOrder($quotation);
            $this->writeLeadNote($quotation, 'ลูกค้าอนุมัติใบเสนอราคา '.$quotation->display_number.' และระบบสร้างใบสั่งผลิต '.$productionOrder->production_order_number);
        });

        if (! $wasApproved) {
            app(LineNotificationService::class)->notifyQuotationApproved($quotation->fresh(['lead', 'productionOrder']));
        }

        return back()->with('success', 'อนุมัติใบเสนอราคาและสร้างใบสั่งผลิตเรียบร้อยแล้ว');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $quotation->delete();

        return redirect()
            ->route('admin.quotations.index')
            ->with('success', 'ลบใบเสนอราคาเรียบร้อยแล้ว');
    }

    public function pdf(Quotation $quotation, QuotationPdfService $pdfService): Response
    {
        $pdf = $pdfService->render($quotation);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$quotation->display_number.'.pdf"',
        ]);
    }

    private function validateQuotation(Request $request): array
    {
        $request->merge([
            'items' => collect($request->input('items', []))
                ->reject(fn (array $item): bool => $this->isEmptyItemRow($item))
                ->values()
                ->all(),
        ]);

        return $request->validate([
            'lead_id' => ['required', 'exists:leads,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'province' => ['required', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Quotation::STATUSES))],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'valid_until' => ['nullable', 'date'],
            'remark' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'items.*.unit' => ['required', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ]);
    }

    private function calculateTotals(array $validated): array
    {
        $subtotal = collect($validated['items'])->sum(fn (array $item): float => round((float) $item['qty'] * (float) $item['unit_price'], 2));
        $discount = (float) ($validated['discount'] ?? 0);
        $shippingCost = (float) ($validated['shipping_cost'] ?? 0);
        $depositAmount = (float) ($validated['deposit_amount'] ?? 0);
        $grandTotal = max(0, $subtotal - $discount + $shippingCost);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'shipping_cost' => round($shippingCost, 2),
            'deposit_amount' => round(min($depositAmount, $grandTotal), 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();

        foreach ($items as $item) {
            $lineTotal = round((float) $item['qty'] * (float) $item['unit_price'], 2);

            $quotation->items()->create([
                'item_name' => $item['item_name'],
                'product_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'qty' => $item['qty'],
                'quantity' => $item['qty'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'total_price' => $lineTotal,
                'subtotal' => $lineTotal,
            ]);
        }
    }

    private function nextQuotationNumber(): string
    {
        $prefix = 'QTN-'.now()->format('Ym').'-';
        $latest = Quotation::where(function ($query) use ($prefix): void {
            $query->where('quotation_no', 'like', $prefix.'%')
                ->orWhere('quotation_number', 'like', $prefix.'%');
        })
            ->lockForUpdate()
            ->orderByDesc(DB::raw('COALESCE(quotation_no, quotation_number)'))
            ->first();

        $latestNumber = $latest?->display_number;
        $next = $latestNumber ? ((int) str_replace($prefix, '', $latestNumber)) + 1 : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function isEmptyItemRow(array $item): bool
    {
        $itemName = trim((string) ($item['item_name'] ?? $item['product_name'] ?? ''));
        $qty = $item['qty'] ?? $item['quantity'] ?? null;
        $unitPrice = $item['unit_price'] ?? null;

        return $itemName === ''
            && ($qty === null || $qty === '' || (float) $qty === 1.0)
            && ($unitPrice === null || $unitPrice === '' || (float) $unitPrice === 0.0);
    }

    private function ensureProductionOrder(Quotation $quotation): ProductionOrder
    {
        $productionOrder = ProductionOrder::firstOrCreate(
            ['quotation_id' => $quotation->id],
            [
                'lead_id' => $quotation->lead_id,
                'production_order_number' => $this->nextProductionOrderNumber(),
                'status' => 'waiting',
                'notes' => 'สร้างอัตโนมัติจากใบเสนอราคา '.$quotation->display_number,
            ]
        );

        if ($productionOrder->items()->doesntExist()) {
            $quotation->loadMissing('items');

            foreach ($quotation->items as $item) {
                $productionOrder->items()->create([
                    'quotation_item_id' => $item->id,
                    'item_name' => $item->display_name,
                    'description' => $item->description,
                    'qty' => $item->display_quantity,
                    'unit' => $item->unit ?: 'ชิ้น',
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->display_total,
                ]);
            }
        }

        return $productionOrder;
    }

    private function nextProductionOrderNumber(): string
    {
        $prefix = 'PO-'.now()->format('Ym').'-';
        $latest = ProductionOrder::where('production_order_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('production_order_number')
            ->first();

        $next = $latest
            ? ((int) str_replace($prefix, '', $latest->production_order_number)) + 1
            : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function writeLeadNote(Quotation $quotation, string $note): void
    {
        $quotation->lead?->notes()->create(['note' => $note]);
    }

    private function leadOptions(?Lead $selectedLead = null)
    {
        $leads = Lead::query()->latest()->take(100)->get();

        if ($selectedLead && ! $leads->contains('id', $selectedLead->id)) {
            $leads->prepend($selectedLead);
        }

        return $leads;
    }
}
