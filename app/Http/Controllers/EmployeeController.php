<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Role;
use App\Models\SalesTarget;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Quotation;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['company', 'companies', 'roles', 'salesTargets'])->withCount('leads')->get();
        $companies = Company::withCount(['leads', 'employees'])->get();
        $roles = \App\Models\Role::all();
        
        $currentMonthDeals = \App\Models\Deal::whereHas('stage', function($q) {
            $q->where('is_won', true);
        })->whereMonth('actual_close_date', now()->month)
          ->whereYear('actual_close_date', now()->year)
          ->get();
          
        $totalSystemPerformance = 0;
        $totalSystemTarget = 0;

        foreach ($employees as $emp) {
            $target = $emp->currentTarget();
            $emp->won_deals_value = $currentMonthDeals->where('assigned_to', $emp->id)->sum('value');
            $emp->performance_progress = 0;
            
            if ($target && $target->target_amount > 0) {
                $emp->performance_progress = min(100, ($emp->won_deals_value / $target->target_amount) * 100);
                $totalSystemTarget += $target->target_amount;
            }
            $totalSystemPerformance += $emp->won_deals_value;
        }

        $systemPerformancePercent = $totalSystemTarget > 0 ? min(100, ($totalSystemPerformance / $totalSystemTarget) * 100) : 0;

        return view('employees.index', compact('employees', 'companies', 'roles', 'systemPerformancePercent'));
    }

    public function edit(Employee $employee)
    {
        $companies = Company::all();
        $roles = \App\Models\Role::all();
        return view('employees.edit', compact('employee', 'companies', 'roles'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:employees',
            'password' => 'required|string|min:6',
            'companies' => 'required|array|min:1',
            'companies.*' => 'exists:companies,id',
        ], [
            'name.required' => __('messages.required'),
            'username.required' => __('messages.required'),
            'username.unique' => __('messages.username_taken'),
            'password.required' => __('messages.required'),
            'password.min' => __('messages.min_password'),
            'companies.required' => __('messages.required'),
        ]);

        $v['password'] = Hash::make($v['password']);
        $v['company_id'] = $request->companies[0]; // Set primary company

        $employee = Employee::create($v);

        if ($request->has('companies')) {
            $employee->companies()->sync($request->companies);
        }

        if ($request->has('roles')) {
            $employee->roles()->sync($request->roles);
        }

        return redirect()->route('employees.index')->with('success', __('messages.employee_added'));
    }

    public function update(Request $request, Employee $employee)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:employees,username,' . $employee->id,
            'password' => 'nullable|string|min:6',
            'companies' => 'required|array|min:1',
            'companies.*' => 'exists:companies,id',
        ]);

        if (!empty($v['password'])) {
            $v['password'] = Hash::make($v['password']);
        } else {
            unset($v['password']);
        }

        $v['company_id'] = $request->companies[0]; // Set primary company

        $employee->update($v);

        if ($request->has('companies')) {
            $employee->companies()->sync($request->companies);
        }

        if ($request->has('roles')) {
            $employee->roles()->sync($request->roles);
        }

        return redirect()->route('employees.index')->with('success', __('messages.employee_updated'));
    }

    /**
     * Admin dedicated password reset for employees
     */
    public function adminUpdatePassword(Request $request, Employee $employee)
    {
        abort_if(!auth()->user()->is_admin, 403);
        
        $v = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $employee->update([
            'password' => Hash::make($v['password'])
        ]);

        return back()->with('success', __('messages.password_updated'));
    }

    public function destroy(Request $request, Employee $employee)
    {
        abort_if(!auth()->user()->is_admin, 403);

        if ($employee->is_admin) {
            return back()->withErrors(['error' => __('messages.cannot_delete_admin')]);
        }

        $request->validate([
            'transfer_to_id' => 'required|exists:employees,id|different:' . $employee->id,
        ]);

        $targetId = $request->transfer_to_id;

        DB::transaction(function () use ($employee, $targetId) {
            // Transfer Leads
            Lead::where('added_by', $employee->id)->update(['added_by' => $targetId]);

            // Transfer Deals
            Deal::where('assigned_to', $employee->id)->update(['assigned_to' => $targetId]);

            // Transfer Customers
            Customer::where('assigned_to', $employee->id)->update(['assigned_to' => $targetId]);

            // Transfer Tasks (both assigned and created)
            Task::where('assigned_to', $employee->id)->update(['assigned_to' => $targetId]);
            Task::where('created_by', $employee->id)->update(['created_by' => $targetId]);

            // Transfer Notes
            Note::where('employee_id', $employee->id)->update(['employee_id' => $targetId]);

            // Transfer Quotations
            Quotation::where('created_by', $employee->id)->update(['created_by' => $targetId]);

            // Transfer Invoices
            Invoice::where('created_by', $employee->id)->update(['created_by' => $targetId]);

            // Soft Delete the employee
            $employee->delete();
        });

        return redirect()->route('employees.index')->with('success', __('messages.employee_deleted_and_transferred') ?? 'Employee deleted and data transferred successfully.');
    }

    public function toggleStatus(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);
        $msg = $employee->is_active ? __('messages.account_activated') : __('messages.account_deactivated');
        return back()->with('success', $msg);
    }

    public function setTarget(Request $request, Employee $employee)
    {
        $v = $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2024',
        ]);

        \App\Models\SalesTarget::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month' => $v['month'],
                'year' => $v['year'],
            ],
            [
                'target_amount' => $v['target_amount'],
                'commission_percentage' => $v['commission_percentage'],
            ]
        );

        return back()->with('success', __('messages.target_set'));
    }

    public function activityLog(Employee $employee)
    {
        $activities = $employee->activities_log()->latest()->paginate(50);
        return view('employees.activity_log', compact('employee', 'activities'));
    }
}
