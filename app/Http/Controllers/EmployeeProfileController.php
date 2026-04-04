<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Deal;
use App\Services\EmployeeActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('web')->user();
        $target = $employee->currentTarget();
        
        // Calculate performance
        $wonDealsValue = Deal::where('assigned_to', $employee->id)
            ->whereHas('stage', function($q) {
                $q->where('is_won', true);
            })
            ->whereMonth('actual_close_date', now()->month)
            ->whereYear('actual_close_date', now()->year)
            ->sum('value');
            
        $performancePercentage = 0;
        if ($target && $target->target_amount > 0) {
            $performancePercentage = min(100, ($wonDealsValue / $target->target_amount) * 100);
        }
        
        $estimatedCommission = 0;
        if ($target) {
            $estimatedCommission = ($wonDealsValue * $target->commission_percentage) / 100;
        }

        $activities = $employee->activities_log()->latest()->take(10)->get();

        return view('employees.profile', compact('employee', 'target', 'wonDealsValue', 'performancePercentage', 'estimatedCommission', 'activities'));
    }

    public function update(Request $request)
    {
        $employee = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $employee->avatar = $path;
        }

        $employee->name = $validated['name'];
        
        if (array_key_exists('phone_number', $validated)) {
            $employee->phone_number = $validated['phone_number'];
        }
        
        if (array_key_exists('job_title', $validated)) {
            $employee->job_title = $validated['job_title'];
        }

        if (!empty($validated['password'])) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->save();
        
        EmployeeActivityLogger::log('profile_update', 'Updated personal profile information');

        return back()->with('success', __('messages.profile_updated'));
    }

    public function updateStatus(Request $request)
    {
        $employee = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'status' => 'required|in:available,site_visit,meeting,on_leave',
        ]);

        $employee->status = $validated['status'];
        $employee->save();
        
        EmployeeActivityLogger::log('status_change', 'Changed presence status to ' . $validated['status']);

        return back()->with('success', __('messages.status_updated'));
    }

    public function updateSignatures(Request $request)
    {
        $employee = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'email_signature' => 'nullable|string',
            'quote_signature' => 'nullable|string',
        ]);

        $employee->email_signature = $validated['email_signature'];
        $employee->quote_signature = $validated['quote_signature'];
        $employee->save();
        
        EmployeeActivityLogger::log('signature_update', 'Updated email/quote signatures');

        return back()->with('success', __('messages.signatures_updated'));
    }

    public function updatePreferences(Request $request)
    {
        $employee = Auth::guard('web')->user();
        
        $employee->notification_preferences = $request->input('notifications', []);
        $employee->working_hours = $request->input('working_hours', []);
        $employee->save();
        
        EmployeeActivityLogger::log('preferences_update', 'Updated notification and working hours preferences');

        return back()->with('success', __('messages.preferences_updated'));
    }
}
