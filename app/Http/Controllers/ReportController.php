<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->is_admin;

        $leadQuery = $isAdmin ? Lead::query() : Lead::where('company_id', $user->company_id);
        $campaignQuery = $isAdmin ? Campaign::query() : Campaign::where('company_id', $user->company_id);
        
        $fromDate = $request->input('from_date', now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        if ($fromDate) {
            $leadQuery->whereDate('created_at', '>=', $fromDate);
            $campaignQuery->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $leadQuery->whereDate('created_at', '<=', $toDate);
            $campaignQuery->whereDate('created_at', '<=', $toDate);
        }

        $totalBudget = (clone $campaignQuery)->sum('budget');
        $totalSpend = (clone $campaignQuery)->sum('total_spend');
        $totalReach = (clone $campaignQuery)->sum('reach');
        $totalImpressions = (clone $campaignQuery)->sum('impressions');
        $totalClicks = (clone $campaignQuery)->sum('clicks');
        $totalConversions = (clone $campaignQuery)->sum('conversions');
        $totalCampaignLeads = (clone $campaignQuery)->sum('leads_generated');

        $avgCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
        $avgCpc = $totalClicks > 0 ? round($totalSpend / $totalClicks, 2) : 0;
        $avgCpl = $totalCampaignLeads > 0 ? round($totalSpend / $totalCampaignLeads, 2) : 0;

        $leadsByStatus = [
            'new' => (clone $leadQuery)->where('status', 'new')->count(),
            'contacted' => (clone $leadQuery)->where('status', 'contacted')->count(),
            'interested' => (clone $leadQuery)->where('status', 'interested')->count(),
            'not_interested' => (clone $leadQuery)->where('status', 'not_interested')->count(),
            'converted' => (clone $leadQuery)->where('status', 'converted')->count(),
        ];

        $leadsByPriority = [
            'low' => (clone $leadQuery)->where('priority', 'low')->count(),
            'medium' => (clone $leadQuery)->where('priority', 'medium')->count(),
            'high' => (clone $leadQuery)->where('priority', 'high')->count(),
            'urgent' => (clone $leadQuery)->where('priority', 'urgent')->count(),
        ];

        $campaignsByStatus = [
            'active' => (clone $campaignQuery)->where('status', 'active')->count(),
            'paused' => (clone $campaignQuery)->where('status', 'paused')->count(),
            'completed' => (clone $campaignQuery)->where('status', 'completed')->count(),
        ];

        $companyQuery = clone ($isAdmin ? Company::query() : Company::where('id', $user->company_id));

        $companies = $companyQuery->get()->map(function($comp) use ($fromDate, $toDate) {
            $lq = $comp->leads();
            $cq = $comp->campaigns();
            
            if ($fromDate) {
                $lq->whereDate('created_at', '>=', $fromDate);
                $cq->whereDate('created_at', '>=', $fromDate);
            }
            if ($toDate) {
                $lq->whereDate('created_at', '<=', $toDate);
                $cq->whereDate('created_at', '<=', $toDate);
            }
            
            $comp->time_leads_count = $lq->count();
            $comp->time_campaigns_count = $cq->count();
            $comp->time_budget = $cq->sum('budget');
            $comp->time_spend = $cq->sum('total_spend');
            
            $comp->cpl = $comp->time_leads_count > 0 ? round($comp->time_spend / $comp->time_leads_count, 2) : 0;
            return $comp;
        });

        $employees = clone ($isAdmin ? Employee::with('company') : Employee::where('id', $user->id));
        $employees = $employees->get()->map(function($emp) use ($fromDate, $toDate) {
            $lq = $emp->leads();
            if ($fromDate) $lq->whereDate('created_at', '>=', $fromDate);
            if ($toDate) $lq->whereDate('created_at', '<=', $toDate);
            $emp->time_leads_count = $lq->count();
            return $emp;
        });

        $last7Days = collect(range(6, 0))->map(function ($i) use ($leadQuery, $toDate) {
            $baseDate = $toDate ? \Carbon\Carbon::parse($toDate) : now();
            $date = $baseDate->subDays($i);
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->translatedFormat('D'),
                'count' => (clone $leadQuery)->whereDate('created_at', $date)->count(),
            ];
        });

        $topCampaigns = (clone $campaignQuery)->orderByDesc('leads_generated')->take(5)->get();

        return view('reports.index', compact(
            'totalBudget', 'totalSpend', 'totalReach', 'totalImpressions', 'totalClicks',
            'totalConversions', 'totalCampaignLeads', 'avgCtr', 'avgCpc', 'avgCpl',
            'leadsByStatus', 'leadsByPriority', 'campaignsByStatus',
            'companies', 'employees', 'last7Days', 'topCampaigns',
            'fromDate', 'toDate'
        ));
    }
}

