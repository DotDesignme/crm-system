<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\Company;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->is_admin) {
            $totalLeads = Lead::count();
            $todayLeads = Lead::whereDate('created_at', today())->count();
            $companies = Company::withCount('leads')->get();
            $employees = Employee::withCount(['leads', 'deals'])->get();
            $recentLeads = Lead::with(['company', 'employee'])->latest()->take(10)->get();
            $myLeads = Lead::where('added_by', $user->id)->count();

            $statusStats = [
                'new' => Lead::where('status', 'new')->count(),
                'contacted' => Lead::where('status', 'contacted')->count(),
                'interested' => Lead::where('status', 'interested')->count(),
                'not_interested' => Lead::where('status', 'not_interested')->count(),
                'converted' => Lead::where('status', 'converted')->count(),
            ];

            // B2B Advanced Metrics
            $totalPipelineValue = Deal::sum('value');
            $weightedRevenue = Deal::sum(DB::raw('value * (probability / 100.0)'));
            $wonDealsValue = Deal::whereHas('stage', fn($q) => $q->where('is_won', true))->sum('value');
            
            $funnelData = DealStage::withCount('deals')->orderBy('order')->get();
            
            // Compatibility for SQLite/MySQL
            $monthExpression = DB::getDriverName() === 'sqlite' 
                ? 'strftime("%Y-%m", created_at)' 
                : 'DATE_FORMAT(created_at, "%Y-%m")';

            $revenueByMonth = Deal::select(
                DB::raw("$monthExpression as month"),
                DB::raw('SUM(value) as total')
            )->groupBy('month')->orderBy('month')->get();

            $activities = Activity::with(['employee', 'activitiable'])->latest()->take(15)->get();

            return view('dashboard', compact(
                'totalLeads', 'todayLeads', 'companies', 'employees', 'recentLeads', 'statusStats',
                'totalPipelineValue', 'weightedRevenue', 'wonDealsValue', 'funnelData', 'revenueByMonth', 'myLeads', 'activities'
            ));
        }

        $company_id = $user->company_id;
        $totalLeads = Lead::where('company_id', $company_id)->count();
        $todayLeads = Lead::where('company_id', $company_id)->whereDate('created_at', today())->count();
        $myLeads = Lead::where('added_by', $user->id)->count();
        $recentLeads = Lead::where('company_id', $company_id)->with('employee')->latest()->take(10)->get();
        
        $activities = Activity::where('company_id', $company_id)->with(['employee', 'activitiable'])->latest()->take(10)->get();

        // Default values for non-admin to prevent View errors
        $companies = collect();
        $employees = collect();
        $weightedRevenue = 0;
        $wonDealsValue = 0;
        $totalPipelineValue = 0;
        $funnelData = collect();
        $statusStats = [];
        $revenueByMonth = collect();

        return view('dashboard', compact(
            'totalLeads', 'todayLeads', 'myLeads', 'recentLeads', 'activities',
            'companies', 'employees', 'weightedRevenue', 'wonDealsValue', 'totalPipelineValue', 
            'funnelData', 'statusStats', 'revenueByMonth'
        ));
    }
}
