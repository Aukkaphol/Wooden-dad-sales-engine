<?php

namespace App\Http\Controllers;

use App\Models\Craftsman;
use App\Models\ProductionOrder;
use App\Services\CostCalculationService;
use App\Services\InventoryService;
use App\Services\LineNotificationService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProductionController extends Controller
{
    public function index(): View
    {
        return view('admin.production.index', [
            'stages' => ProductionOrder::STATUSES,
            'ordersByStage' => ProductionOrder::query()
                ->with(['lead', 'quotation', 'craftsmen'])
                ->latest()
                ->get()
                ->groupBy('status'),
            'counts' => ProductionOrder::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function show(ProductionOrder $productionOrder, CostCalculationService $costCalculationService, PurchaseService $purchaseService): View
    {
        $productionOrder->load(['lead', 'quotation.items', 'craftsmen']);
        $inventoryService = app(InventoryService::class);

        return view('admin.production.show', [
            'productionOrder' => $productionOrder,
            'stages' => ProductionOrder::STATUSES,
            'craftsmen' => Craftsman::where('is_active', true)->orderBy('name')->get(),
            'costSummary' => $costCalculationService->productionOrderCost($productionOrder),
            'materialShortages' => $purchaseService->shortageForProduction($productionOrder),
            'materialRequirements' => $inventoryService->materialRequirements($productionOrder),
        ]);
    }

    public function updateStatus(Request $request, ProductionOrder $productionOrder, InventoryService $inventoryService, LineNotificationService $lineNotificationService): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(ProductionOrder::STATUSES))],
        ]);

        $oldStatusKey = $productionOrder->status;

        if ($oldStatusKey === 'waiting' && ! in_array($validated['status'], ['waiting', 'cutting'], true)) {
            return back()->withErrors(['status' => 'ต้องเริ่มงานผลิตจากสถานะกำลังตัดไม้ก่อน เพื่อให้ระบบตรวจและตัดสต๊อกวัสดุ']);
        }

        if ($oldStatusKey === 'waiting' && $validated['status'] === 'cutting') {
            $shortages = $inventoryService->deductForProductionStart($productionOrder);

            if ($shortages->isNotEmpty()) {
                $purchaseRequests = app(PurchaseService::class)->createPurchaseRequestsForShortages($productionOrder, $shortages);
                $purchaseRequests->each(function ($purchaseRequest) use ($productionOrder, $lineNotificationService): void {
                    $lineNotificationService->notifyMaterialShortage(
                        $productionOrder,
                        $purchaseRequest->material->name,
                        (float) $purchaseRequest->requested_qty,
                        $purchaseRequest->material->unit,
                        $purchaseRequest->pr_no
                    );
                });

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'วัสดุไม่เพียงพอ ระบบสร้าง PR แล้ว',
                        'shortages' => $shortages->values(),
                    ], 422);
                }

                return back()
                    ->withErrors(['materials' => 'วัสดุไม่เพียงพอ ระบบสร้าง PR อัตโนมัติแล้ว'])
                    ->with('warning', 'วัสดุไม่เพียงพอ ระบบสร้าง PR อัตโนมัติแล้ว');
            }
        }

        $oldStatus = $productionOrder->status_label;
        $payload = ['status' => $validated['status']];

        if ($validated['status'] !== 'waiting' && ! $productionOrder->started_at) {
            $payload['started_at'] = now();
        }

        if ($validated['status'] === 'delivered') {
            $payload['completed_at'] = now();
        }

        $productionOrder->update($payload);
        $productionOrder->refresh();

        $newStatus = $productionOrder->fresh()->status_label;
        $productionOrder->lead->notes()->create([
            'note' => "เนเธเธชเธฑเนเธเธเธฅเธดเธ• {$productionOrder->production_order_number} เธขเนเธฒเธขเธเธฒเธ {$oldStatus} เน€เธเนเธ {$newStatus}",
        ]);

        if ($oldStatusKey === 'waiting' && $productionOrder->status !== 'waiting') {
            $lineNotificationService->notifyProductionStarted($productionOrder->fresh(['lead']));
        }

        if ($oldStatusKey !== 'ready_delivery' && $productionOrder->status === 'ready_delivery') {
            $lineNotificationService->notifyProductionReady($productionOrder->fresh(['lead']));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'production_order_id' => $productionOrder->id,
                'status' => $productionOrder->status,
                'status_label' => $newStatus,
            ]);
        }

        return back()->with('success', 'เธญเธฑเธเน€เธ”เธ•เธชเธ–เธฒเธเธฐเธเธฒเธเธเธฅเธดเธ•เน€เธฃเธตเธขเธเธฃเนเธญเธขเนเธฅเนเธง');
    }

    public function assignCraftsmen(Request $request, ProductionOrder $productionOrder, LineNotificationService $lineNotificationService): RedirectResponse
    {
        $validated = $request->validate([
            'craftsman_ids' => ['nullable', 'array'],
            'craftsman_ids.*' => ['integer', 'exists:craftsmen,id'],
            'new_craftsman_name' => ['nullable', 'string', 'max:255'],
            'new_craftsman_phone' => ['nullable', 'string', 'max:50'],
            'delivery_date' => ['nullable', 'date'],
            'installation_date' => ['nullable', 'date'],
            'installation_status' => ['nullable', 'string', 'in:pending,scheduled,installed,delayed'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $oldDeliveryDate = $productionOrder->delivery_date?->format('Y-m-d');
        $oldInstallationDate = $productionOrder->installation_date?->format('Y-m-d');
        $oldInstallationStatus = $productionOrder->installation_status;
        $craftsmanIds = $validated['craftsman_ids'] ?? [];

        if (! empty($validated['new_craftsman_name'])) {
            $craftsman = Craftsman::create([
                'name' => $validated['new_craftsman_name'],
                'phone' => $validated['new_craftsman_phone'] ?? null,
            ]);
            $craftsmanIds[] = $craftsman->id;
        }

        $productionOrder->craftsmen()->sync($craftsmanIds);
        $productionOrder->update([
            'delivery_date' => $validated['delivery_date'] ?? null,
            'installation_date' => $validated['installation_date'] ?? null,
            'installation_status' => $validated['installation_status'] ?? 'pending',
            'delivery_address' => $validated['delivery_address'] ?? null,
            'delivery_cost' => $validated['delivery_cost'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        $freshProductionOrder = $productionOrder->fresh(['lead', 'craftsmen']);
        $names = $freshProductionOrder->craftsmen->pluck('name')->implode(', ') ?: 'ยังไม่ระบุช่าง';
        $freshProductionOrder->lead->notes()->create([
            'note' => "อัปเดตช่างผู้รับผิดชอบใบสั่งผลิต {$freshProductionOrder->production_order_number}: {$names}",
        ]);

        $newDeliveryDate = $freshProductionOrder->delivery_date?->format('Y-m-d');
        $newInstallationDate = $freshProductionOrder->installation_date?->format('Y-m-d');
        if (($newDeliveryDate && $newDeliveryDate !== $oldDeliveryDate) || ($newInstallationDate && $newInstallationDate !== $oldInstallationDate)) {
            $lineNotificationService->notifyDeliveryScheduled($freshProductionOrder);
        }

        if ($oldInstallationStatus !== 'installed' && $freshProductionOrder->installation_status === 'installed') {
            $lineNotificationService->notifyInstallationCompleted($freshProductionOrder);
        }

        return back()->with('success', 'อัปเดตทีมผลิตเรียบร้อยแล้ว');
    }
}
