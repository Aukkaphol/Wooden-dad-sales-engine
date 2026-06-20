<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Quotation;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminMarketingCenterController extends Controller
{
    public function campaigns(): View
    {
        return view('admin.marketing.campaigns', [
            'sources' => [
                'Website' => Lead::where('source_platform', 'website')->orWhere('source', 'website')->orWhereNull('source')->count(),
                'Facebook' => Lead::where('source_platform', 'facebook')->orWhereIn('source', ['facebook', 'facebook_lead_ads', 'facebook_messenger'])->count(),
                'LINE OA' => Lead::where('source_platform', 'line')->orWhereIn('source', ['line', 'line_oa'])->count(),
            ],
        ]);
    }

    public function analytics(): View
    {
        $months = collect(range(5, 0))->map(fn (int $offset): string => now()->subMonths($offset)->format('Y-m'));

        return view('admin.marketing.analytics', [
            'rows' => $months->map(function (string $month): array {
                $leads = Lead::whereYear('created_at', Carbon::parse($month.'-01')->year)
                    ->whereMonth('created_at', Carbon::parse($month.'-01')->month)
                    ->get();
                $won = $leads->filter(fn (Lead $lead): bool => ($lead->status ?: $lead->lead_status) === 'won')->count();

                return [
                    'month' => $month,
                    'website' => $leads->filter(fn (Lead $lead): bool => in_array($lead->source_platform ?: $lead->source, [null, '', 'website'], true))->count(),
                    'facebook' => $leads->filter(fn (Lead $lead): bool => in_array($lead->source_platform ?: $lead->source, ['facebook', 'facebook_lead_ads', 'facebook_messenger'], true))->count(),
                    'line' => $leads->filter(fn (Lead $lead): bool => in_array($lead->source_platform ?: $lead->source, ['line', 'line_oa'], true))->count(),
                    'conversion' => $leads->count() > 0 ? round(($won / $leads->count()) * 100, 1) : 0,
                    'forecast' => Quotation::whereIn('status', ['draft', 'sent'])
                        ->whereYear('created_at', Carbon::parse($month.'-01')->year)
                        ->whereMonth('created_at', Carbon::parse($month.'-01')->month)
                        ->sum('grand_total'),
                ];
            }),
        ]);
    }

    public function usersRoles(): View
    {
        return view('admin.settings.users-roles');
    }
}
