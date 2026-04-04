<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contract;
use App\Models\Deal;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\LossReason;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminExecutiveController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // 1. Sales Performance & Leaderboard
        $employees = Employee::withCount([
            'leads as total_leads' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            },
            'deals as won_deals_count' => function ($query) use ($start, $end) {
                $query->whereHas('stage', fn($q) => $q->where('is_won', true))
                      ->whereBetween('actual_close_date', [$start, $end]);
            }
        ])
        ->withSum(['deals as won_revenue' => function ($query) use ($start, $end) {
            $query->whereHas('stage', fn($q) => $q->where('is_won', true))
                  ->whereBetween('actual_close_date', [$start, $end]);
        }], 'value')
        ->get()
        ->map(function ($employee) {
            $employee->conversion_rate = $employee->total_leads > 0 
                ? round(($employee->won_deals_count / $employee->total_leads) * 100, 2) 
                : 0;
            return $employee;
        })
        ->sortByDesc('won_revenue')
        ->values();

        // 2. Lead Leakage (Loss Analysis)
        $lossStats = LossReason::withCount(['deals' => function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }])
        ->get()
        ->filter(fn($r) => $r->deals_count > 0)
        ->values();

        // 3. Campaign ROI Matrix
        $campaigns = Campaign::withCount([
            'leads as total_leads',
            'leads as qualified_leads' => fn($q) => $q->where('status', '!=', 'new')
        ])
        ->get()
        ->map(function ($campaign) use ($start, $end) {
            $wonDeals = $campaign->deals()
                ->whereHas('stage', fn($q) => $q->where('is_won', true))
                ->whereBetween('actual_close_date', [$start, $end])
                ->get();

            $revenue = $wonDeals->sum('value');
            $wonCount = $wonDeals->count();

            $campaign->won_revenue = $revenue;
            $campaign->won_count = $wonCount;
            $campaign->cpl = $campaign->total_leads > 0 ? round($campaign->total_spend / $campaign->total_leads, 2) : 0;
            $campaign->cpql = $campaign->qualified_leads > 0 ? round($campaign->total_spend / $campaign->qualified_leads, 2) : 0;
            $campaign->cac = $wonCount > 0 ? round($campaign->total_spend / $wonCount, 2) : 0;
            $campaign->roi = $campaign->total_spend > 0 ? round((($revenue - $campaign->total_spend) / $campaign->total_spend) * 100, 2) : 0;

            return $campaign;
        });

        $goldenCampaign = $campaigns->sortByDesc('roi')->first();

        // 4. Global Funnel Analytics
        $funnel = [
            'leads' => Lead::whereBetween('created_at', [$start, $end])->count(),
            'meetings' => Lead::whereHas('tasks', function($q) use ($start, $end) {
                $q->where('type', 'meeting')->whereBetween('created_at', [$start, $end]);
            })->count(),
            'quotations' => Quotation::whereBetween('created_at', [$start, $end])->count(),
            'contracts' => Contract::whereBetween('created_at', [$start, $end])->count(),
        ];

        return view('admin.executive', compact(
            'employees', 'lossStats', 'campaigns', 'goldenCampaign', 'funnel', 'startDate', 'endDate'
        ));
    }
}
