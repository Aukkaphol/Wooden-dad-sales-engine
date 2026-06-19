<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminLeadController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $query = $this->filteredLeads($filters);
        $totalLeads = Lead::count();
        $completedLeads = Lead::where('lead_status', 'completed')->count();
        $allLeads = Lead::all(['budget', 'lead_status']);

        return view('admin.leads', [
            'leads' => $query->latest()->paginate(15)->withQueryString(),
            'filters' => $filters,
            'provinces' => Lead::query()
                ->select('province')
                ->whereNotNull('province')
                ->distinct()
                ->orderBy('province')
                ->pluck('province'),
            'statuses' => Lead::PIPELINE_STATUSES,
            'statusCounts' => [
                'new' => Lead::where('lead_status', 'new')->count(),
                'contacted' => Lead::where('lead_status', 'contacted')->count(),
                'quoted' => Lead::where('lead_status', 'quoted')->count(),
                'deposit_paid' => Lead::where('lead_status', 'deposit_paid')->count(),
                'completed' => $completedLeads,
            ],
            'kpis' => [
                'total_leads' => $totalLeads,
                'estimated_revenue' => $allLeads->sum(fn (Lead $lead) => $this->estimatedBudgetValue($lead->budget)),
                'deposit_revenue' => $allLeads
                    ->whereIn('lead_status', ['deposit_paid', 'production', 'installation', 'completed'])
                    ->sum(fn (Lead $lead) => $this->estimatedBudgetValue($lead->budget) * 0.3),
                'conversion_rate' => $totalLeads > 0 ? round(($completedLeads / $totalLeads) * 100, 1) : 0,
            ],
            'widgets' => [
                'leads_today' => Lead::whereDate('created_at', today())->count(),
                'leads_this_month' => Lead::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                'pending_follow_up' => Lead::whereNotNull('follow_up_date')
                    ->whereDate('follow_up_date', '<=', today())
                    ->whereNotIn('lead_status', ['completed', 'lost'])
                    ->count(),
            ],
            'kanbanStages' => [
                'new' => 'ลีดใหม่',
                'contacted' => 'ติดต่อแล้ว',
                'quoted' => 'เสนอราคาแล้ว',
                'deposit_paid' => 'รับมัดจำแล้ว',
                'completed' => 'ปิดงานแล้ว',
            ],
            'kanbanLeads' => Lead::query()
                ->whereIn('lead_status', ['new', 'contacted', 'quoted', 'deposit_paid', 'completed'])
                ->latest()
                ->get()
                ->groupBy('lead_status'),
            'monthlyLeads' => $this->monthlyLeads(),
            'provinceStats' => $this->provinceStats(),
            'conversionRate' => $totalLeads > 0 ? round(($completedLeads / $totalLeads) * 100, 1) : 0,
        ]);
    }

    public function show(Lead $lead): View
    {
        $lead->load(['notes', 'quotations.items', 'productionOrders.quotation', 'productionOrders.craftsmen']);

        return view('admin.lead-show', [
            'lead' => $lead,
            'statuses' => Lead::PIPELINE_STATUSES,
            'quotationStatuses' => Lead::QUOTATION_STATUSES,
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'lead_status' => ['required', 'string', 'in:'.implode(',', array_keys(Lead::PIPELINE_STATUSES))],
            'quotation_status' => ['required', 'string', 'in:'.implode(',', array_keys(Lead::QUOTATION_STATUSES))],
            'follow_up_date' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $lead->update($validated);

        return back()->with('success', 'บันทึกรายละเอียดลูกค้าเรียบร้อยแล้ว');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'lead_status' => ['required', 'string', 'in:'.implode(',', array_keys(Lead::PIPELINE_STATUSES))],
        ]);

        $oldStatus = $lead->lead_status_label;
        $lead->update(['lead_status' => $validated['lead_status']]);
        $newStatus = $lead->fresh()->lead_status_label;

        $lead->notes()->create([
            'note' => "ย้ายสถานะงานขายจาก {$oldStatus} เป็น {$newStatus}",
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'lead_id' => $lead->id,
                'lead_status' => $lead->lead_status,
                'lead_status_label' => $newStatus,
                'message' => 'อัปเดตสถานะงานขายเรียบร้อยแล้ว',
            ]);
        }

        return back()->with('success', 'อัปเดตสถานะงานขายเรียบร้อยแล้ว');
    }

    public function storeNote(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $lead->notes()->create($validated);

        return back()->with('success', 'เพิ่มบันทึกติดตามลูกค้าเรียบร้อยแล้ว');
    }

    public function export(Request $request): Response
    {
        $filters = $this->validatedFilters($request);
        $leads = $this->filteredLeads($filters)->latest()->get();
        $filename = 'wooden-dad-leads-'.now()->format('Ymd-His').'.xls';

        return response($this->excelTable($leads), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'lead_status' => ['nullable', 'string', 'in:'.implode(',', array_keys(Lead::PIPELINE_STATUSES))],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
    }

    private function filteredLeads(array $filters): Builder
    {
        return Lead::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['province'] ?? null, fn (Builder $query, string $province) => $query->where('province', $province))
            ->when($filters['lead_status'] ?? null, fn (Builder $query, string $status) => $query->where('lead_status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date));
    }

    private function monthlyLeads()
    {
        return Lead::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function provinceStats()
    {
        return Lead::query()
            ->selectRaw('province, COUNT(*) as total')
            ->whereNotNull('province')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }

    private function estimatedBudgetValue(?string $budget): float
    {
        preg_match_all('/[\d,]+/', (string) $budget, $matches);

        $numbers = collect($matches[0])
            ->map(fn (string $number) => (float) str_replace(',', '', $number))
            ->filter(fn (float $number) => $number > 0)
            ->values();

        if ($numbers->count() >= 2) {
            return ($numbers[0] + $numbers[1]) / 2;
        }

        return (float) ($numbers[0] ?? 0);
    }

    private function excelTable($leads): string
    {
        $rows = $leads->map(function (Lead $lead): string {
            return '<tr>'.
                '<td>'.$this->escapeExcel($lead->created_at?->format('Y-m-d H:i')).'</td>'.
                '<td>'.$this->escapeExcel($lead->name).'</td>'.
                '<td>'.$this->escapeExcel($lead->phone).'</td>'.
                '<td>'.$this->escapeExcel($lead->province).'</td>'.
                '<td>'.$this->escapeExcel($lead->budget).'</td>'.
                '<td>'.$this->escapeExcel($lead->room_width).'</td>'.
                '<td>'.$this->escapeExcel($lead->room_length).'</td>'.
                '<td>'.$this->escapeExcel($lead->lead_status_label).'</td>'.
                '<td>'.$this->escapeExcel($lead->quotation_status_label).'</td>'.
                '<td>'.$this->escapeExcel($lead->follow_up_date?->format('Y-m-d')).'</td>'.
                '<td>'.$this->escapeExcel($lead->message).'</td>'.
                '<td>'.$this->escapeExcel($lead->admin_notes).'</td>'.
            '</tr>';
        })->implode('');

        return "\xEF\xBB\xBF".'<!doctype html><html><head><meta charset="UTF-8"></head><body><table border="1">'.
            '<thead><tr>'.
            '<th>วันที่</th><th>ชื่อ</th><th>เบอร์โทร</th><th>จังหวัด</th><th>งบประมาณ</th>'.
            '<th>ความกว้างห้อง</th><th>ความยาวห้อง</th><th>สถานะ CRM</th><th>สถานะใบเสนอราคา</th><th>วันติดตาม</th><th>ข้อความ</th><th>โน้ตแอดมิน</th>'.
            '</tr></thead><tbody>'.$rows.'</tbody></table></body></html>';
    }

    private function escapeExcel(mixed $value): string
    {
        return e((string) $value);
    }
}
