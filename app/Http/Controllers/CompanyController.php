<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmployeeActivity;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['leads', 'employees'])->get();
        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        abort_if(!auth()->user()->is_admin, 403);
        
        $company->loadCount(['leads', 'employees', 'customers']);
        
        // Performance stats
        $wonDealsCount = \App\Models\Deal::where('company_id', $company->id)
            ->whereHas('stage', fn($q) => $q->where('is_won', true))
            ->count();
            
        $totalRevenue = \App\Models\Deal::where('company_id', $company->id)
            ->whereHas('stage', fn($q) => $q->where('is_won', true))
            ->sum('value');

        $employees = $company->employees()->withCount('leads')->get();
        $settings = \App\Models\SystemSetting::allCached();

        return view('companies.show', compact('company', 'wonDealsCount', 'totalRevenue', 'employees', 'settings'));
    }

    public function edit(Company $company)
    {
        abort_if(!auth()->user()->is_admin, 403);
        return view('companies.edit', compact('company'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->is_admin, 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
        ], [
            'name.required' => __('messages.required'),
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')->with('success', __('messages.company_added'));
    }

    public function update(Request $request, Company $company)
    {
        abort_if(!auth()->user()->is_admin, 403);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')->with('success', __('messages.company_updated'));
    }

    public function destroy(Company $company)
    {
        abort_if(!auth()->user()->is_admin, 403);
        $company->delete();
        return redirect()->route('companies.index')->with('success', __('messages.company_deleted'));
    }

    public function activityLog(Company $company)
    {
        abort_if(!auth()->user()->is_admin, 403);
        
        $employeeIds = $company->employees()->pluck('employees.id');
        
        $activities = EmployeeActivity::whereIn('employee_id', $employeeIds)
            ->with('employee')
            ->latest()
            ->paginate(30);

        return view('companies.activity_log', compact('company', 'activities'));
    }
}
