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
    public function create(Lead $lead): View
    {
        return view('admin.quotations.create', [
            'lead' => $lead,
            'statuses' => Quotation::STATUSES,
        ]);
    }

    public function store(Request $request, Lead $lead): RedirectResponse
    {
        $request->merge([
            'items' => collect($request->input('items', []))
                ->reject(fn (array $item): bool => $this->isEmptyItemRow($item))
                ->values()
                ->all(),
        ]);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Quotation::STATUSES))],
            'notes' => ['nullable', 'string', 'max:3000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999'],
        ]);

        $quotation = DB::transaction(function () use ($lead, $validated): Quotation {
            $quotation = $lead->quotations()->create([
                'quotation_number' => $this->nextQuotationNumber(),
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
            ]);

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $lineSubtotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $subtotal += $lineSubtotal;

                $quotation->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $quotation->update(['subtotal' => $subtotal]);
            $lead->notes()->create([
                'note' => 'เธชเธฃเนเธฒเธเนเธเน€เธชเธเธญเธฃเธฒเธเธฒ '.$quotation->quotation_number.' เธกเธนเธฅเธเนเธฒเธฃเธงเธก เธฟ'.number_format($subtotal, 2),
            ]);

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
            ->with('success', 'เธชเธฃเนเธฒเธเนเธเน€เธชเธเธญเธฃเธฒเธเธฒเน€เธฃเธตเธขเธเธฃเนเธญเธขเนเธฅเนเธง');
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

    public function updateStatus(Request $request, Quotation $quotation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Quotation::STATUSES))],
        ]);

        $wasApproved = $quotation->status === 'approved';
        $shouldNotifyApproved = false;

        DB::transaction(function () use ($quotation, $validated, $wasApproved, &$shouldNotifyApproved): void {
            $quotation->update($validated);

            $quotation->lead->notes()->create([
                'note' => 'อัปเดตสถานะใบเสนอราคา '.$quotation->quotation_number.' เป็น '.$quotation->status_label,
            ]);

            if ($quotation->status === 'approved') {
                $productionOrder = $this->ensureProductionOrder($quotation);
                $shouldNotifyApproved = ! $wasApproved;
                $quotation->lead->notes()->create([
                    'note' => 'สร้างใบสั่งผลิต '.$productionOrder->production_order_number.' จากใบเสนอราคาที่อนุมัติ '.$quotation->quotation_number,
                ]);
            }
        });

        if ($shouldNotifyApproved) {
            app(LineNotificationService::class)->notifyQuotationApproved($quotation->fresh(['lead', 'productionOrder']));
        }

        return back()->with('success', 'อัปเดตสถานะใบเสนอราคาเรียบร้อยแล้ว');
    }

    public function pdf(Quotation $quotation, QuotationPdfService $pdfService): Response
    {
        $pdf = $pdfService->render($quotation);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$quotation->quotation_number.'.pdf"',
        ]);
    }

    private function nextQuotationNumber(): string
    {
        $prefix = 'QTN-'.now()->format('Ym').'-';
        $latest = Quotation::where('quotation_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('quotation_number')
            ->first();

        $next = $latest
            ? ((int) str_replace($prefix, '', $latest->quotation_number)) + 1
            : 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function isEmptyItemRow(array $item): bool
    {
        $productName = trim((string) ($item['product_name'] ?? ''));
        $quantity = $item['quantity'] ?? null;
        $unitPrice = $item['unit_price'] ?? null;

        return $productName === ''
            && ($quantity === null || $quantity === '' || (float) $quantity === 1.0)
            && ($unitPrice === null || $unitPrice === '' || (float) $unitPrice === 0.0);
    }

    private function ensureProductionOrder(Quotation $quotation): ProductionOrder
    {
        return ProductionOrder::firstOrCreate(
            ['quotation_id' => $quotation->id],
            [
                'lead_id' => $quotation->lead_id,
                'production_order_number' => $this->nextProductionOrderNumber(),
                'status' => 'waiting',
            ]
        );
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
}
