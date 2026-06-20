<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\ProductionOrder;
use App\Models\Quotation;
use App\Services\CostCalculationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(CostCalculationService $costCalculationService): View
    {
        $leads = Lead::query()->oldest()->get();
        $quotations = Quotation::query()->with(['lead', 'items'])->oldest()->get();
        $approvedQuotations = $quotations->where('status', 'approved');
        $approvedCosts = $approvedQuotations->map(fn (Quotation $quotation): array => [
            'quotation' => $quotation,
            'cost' => $costCalculationService->quotationCost($quotation),
        ]);

        $totalLeads = $leads->count();
        $wonLeads = $leads->filter(fn (Lead $lead): bool => in_array($lead->status ?: $lead->lead_status, ['won'], true))->count();
        $salesThisMonth = $approvedCosts
            ->filter(fn (array $row): bool => $row['quotation']->approved_at?->isSameMonth(now()) || $row['quotation']->updated_at->isSameMonth(now()))
            ->sum(fn (array $row): float => $row['cost']['selling_price']);

        return view('admin.dashboard', [
            'metrics' => [
                'total_leads' => $totalLeads,
                'website_leads' => $this->sourceCount($leads, 'website'),
                'facebook_leads' => $this->sourceCount($leads, 'facebook'),
                'line_leads' => $this->sourceCount($leads, 'line'),
                'pending_quotations' => $quotations->whereIn('status', ['draft', 'sent'])->count(),
                'active_production' => ProductionOrder::query()->whereNotIn('status', ['delivered', 'completed'])->count(),
                'sales_this_month' => round($salesThisMonth, 2),
                'conversion_rate' => $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0,
            ],
            'leadGrowth' => $this->monthlyLeadGrowth($leads),
            'leadSourceMonths' => $this->monthlyLeadsBySource($leads),
            'conversionByMonth' => $this->monthlyConversion($leads),
            'salesForecast' => $this->salesForecast($quotations),
        ]);
    }

    private function sourceCount(Collection $leads, string $source): int
    {
        return $leads->filter(fn (Lead $lead): bool => $this->sourceGroup($lead) === $source)->count();
    }

    private function sourceGroup(Lead $lead): string
    {
        $source = $lead->source_platform ?: $lead->source;

        return match ($source) {
            'facebook', 'facebook_lead_ads', 'facebook_messenger' => 'facebook',
            'line', 'line_oa' => 'line',
            'manual' => 'manual',
            default => 'website',
        };
    }

    private function monthlyLeadGrowth(Collection $leads): Collection
    {
        return $this->lastTwelveMonths()->map(function (string $month) use ($leads): array {
            $rows = $leads->filter(fn (Lead $lead): bool => $lead->created_at->format('Y-m') === $month);

            return [
                'month' => $month,
                'label' => $rows->first()?->created_at->format('M Y') ?? Carbon::parse($month.'-01')->format('M Y'),
                'value' => $rows->count(),
            ];
        });
    }

    private function monthlyLeadsBySource(Collection $leads): Collection
    {
        return $this->lastTwelveMonths()->map(function (string $month) use ($leads): array {
            $rows = $leads->filter(fn (Lead $lead): bool => $lead->created_at->format('Y-m') === $month);

            return [
                'month' => $month,
                'label' => Carbon::parse($month.'-01')->format('M Y'),
                'website' => $rows->filter(fn (Lead $lead): bool => $this->sourceGroup($lead) === 'website')->count(),
                'facebook' => $rows->filter(fn (Lead $lead): bool => $this->sourceGroup($lead) === 'facebook')->count(),
                'line' => $rows->filter(fn (Lead $lead): bool => $this->sourceGroup($lead) === 'line')->count(),
            ];
        });
    }

    private function monthlyConversion(Collection $leads): Collection
    {
        return $this->lastTwelveMonths()->map(function (string $month) use ($leads): array {
            $rows = $leads->filter(fn (Lead $lead): bool => $lead->created_at->format('Y-m') === $month);
            $won = $rows->filter(fn (Lead $lead): bool => in_array($lead->status ?: $lead->lead_status, ['won'], true))->count();

            return [
                'month' => $month,
                'label' => Carbon::parse($month.'-01')->format('M Y'),
                'value' => $rows->count() > 0 ? round(($won / $rows->count()) * 100, 1) : 0,
            ];
        });
    }

    private function salesForecast(Collection $quotations): Collection
    {
        return $this->lastTwelveMonths()->map(function (string $month) use ($quotations): array {
            $value = $quotations
                ->filter(fn (Quotation $quotation): bool => $quotation->created_at->format('Y-m') === $month)
                ->filter(fn (Quotation $quotation): bool => in_array($quotation->status, ['draft', 'sent'], true))
                ->sum(fn (Quotation $quotation): float => (float) ($quotation->grand_total ?: $quotation->subtotal));

            return [
                'month' => $month,
                'label' => Carbon::parse($month.'-01')->format('M Y'),
                'value' => round($value, 2),
            ];
        });
    }

    private function lastTwelveMonths(): Collection
    {
        return collect(range(11, 0))
            ->map(fn (int $offset): string => now()->subMonths($offset)->format('Y-m'));
    }
}
