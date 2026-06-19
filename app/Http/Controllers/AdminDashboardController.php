<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\Quotation;
use App\Services\CostCalculationService;
use App\Services\PurchaseService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(CostCalculationService $costCalculationService, PurchaseService $purchaseService): View
    {
        $quotations = Quotation::query()
            ->with(['lead', 'items'])
            ->oldest()
            ->get();
        $approvedQuotations = $quotations->where('status', 'approved');
        $quotationCosts = $quotations->map(fn (Quotation $quotation): array => [
            'quotation' => $quotation,
            'cost' => $costCalculationService->quotationCost($quotation),
        ]);
        $approvedCosts = $quotationCosts->filter(fn (array $row): bool => $row['quotation']->status === 'approved');
        $totalRevenue = $approvedCosts->sum(fn (array $row): float => $row['cost']['selling_price']);
        $estimatedProfit = $approvedCosts->sum(fn (array $row): float => $row['cost']['gross_profit']);
        $totalQuotations = $quotations->count();
        $topProducts = $costCalculationService->profitabilityByProduct();

        return view('admin.dashboard', [
            'metrics' => [
                'total_leads' => Lead::count(),
                'total_quotations' => $totalQuotations,
                'approved_quotations' => $approvedQuotations->count(),
                'pending_quotations' => $quotations->whereIn('status', ['draft', 'sent'])->count(),
                'production_orders' => ProductionOrder::count(),
                'total_revenue' => round($totalRevenue, 2),
                'estimated_profit' => round($estimatedProfit, 2),
                'conversion_rate' => $totalQuotations > 0 ? round(($approvedQuotations->count() / $totalQuotations) * 100, 2) : 0.0,
            ],
            'leadsByMonth' => $this->monthlyCounts(Lead::query()->oldest()->get()),
            'revenueByMonth' => $this->monthlyMoney($approvedCosts, 'selling_price'),
            'profitByMonth' => $this->monthlyMoney($approvedCosts, 'gross_profit'),
            'productionSummary' => ProductionOrder::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'lowStockMaterials' => Material::query()
                ->whereColumn('current_stock', '<=', 'low_stock_level')
                ->orderBy('name')
                ->take(8)
                ->get(),
            'topProducts' => $topProducts
                ->sortByDesc('revenue')
                ->take(10)
                ->values(),
            'topSellingProducts' => $topProducts
                ->sortByDesc('quantity_sold')
                ->take(10)
                ->values(),
            'lowStockSuggestions' => $purchaseService->lowStockSuggestions()->take(5),
            'topCustomers' => $this->topCustomers($approvedCosts),
        ]);
    }

    private function monthlyCounts(Collection $records): Collection
    {
        return $records
            ->groupBy(fn ($record): string => $record->created_at->format('Y-m'))
            ->map(fn (Collection $rows, string $month): array => [
                'month' => $month,
                'label' => $rows->first()->created_at->format('M Y'),
                'value' => $rows->count(),
            ])
            ->values();
    }

    private function monthlyMoney(Collection $costRows, string $field): Collection
    {
        return $costRows
            ->groupBy(fn (array $row): string => $row['quotation']->created_at->format('Y-m'))
            ->map(fn (Collection $rows, string $month): array => [
                'month' => $month,
                'label' => $rows->first()['quotation']->created_at->format('M Y'),
                'value' => round($rows->sum(fn (array $row): float => $row['cost'][$field]), 2),
            ])
            ->values();
    }

    private function topCustomers(Collection $approvedCosts): Collection
    {
        return $approvedCosts
            ->groupBy(fn (array $row): string => (string) $row['quotation']->lead_id)
            ->map(function (Collection $rows): array {
                $lead = $rows->first()['quotation']->lead;

                return [
                    'customer' => $lead?->name ?? '-',
                    'quotation_count' => $rows->count(),
                    'revenue' => round($rows->sum(fn (array $row): float => $row['cost']['selling_price']), 2),
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values();
    }
}
