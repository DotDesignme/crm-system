<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    public function index(Request $request)
    {
        $settings = SystemSetting::allCached();
        $activeTab = $request->get('tab', 'branding');
        
        $dealStages = \App\Models\DealStage::orderBy('order')->get();
        $lossReasons = \App\Models\LossReason::orderBy('reason')->get();
        $plugins = \App\Models\Plugin::all();
        
        return view('settings.branding', compact('settings', 'activeTab', 'dealStages', 'lossReasons', 'plugins'));
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'system_icon' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:1024',
            'system_slogan' => 'nullable|string|max:500',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        if ($request->has('app_name')) {
            SystemSetting::set('app_name', $request->app_name);
        }

        if ($request->has('system_icon')) {
            SystemSetting::set('system_icon', $request->system_icon);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            SystemSetting::set('system_logo', $path);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            SystemSetting::set('system_favicon', $path);
        }

        if ($request->has('system_slogan')) {
            SystemSetting::set('system_slogan', $request->system_slogan);
        }

        if ($request->has('primary_color')) {
            SystemSetting::set('primary_color', $request->primary_color);
        }

        if ($request->has('accent_color')) {
            SystemSetting::set('accent_color', $request->accent_color);
        }

        return back()->with('success', __('messages.settings_updated') ?? 'Branding updated successfully');
    }

    public function updateCompany(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_tax_id' => 'nullable|string|max:100',
            'company_cr_number' => 'nullable|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', __('messages.settings_updated') ?? 'Company identity updated successfully');
    }

    public function updateFinancials(Request $request)
    {
        $data = $request->validate([
            'system_currency' => 'required|string|max:10',
            'system_currency_symbol' => 'required|string|max:10',
            'system_vat_percentage' => 'required|numeric|min:0|max:100',
            'system_wht_percentage' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', __('messages.settings_updated') ?? 'Financial settings updated successfully');
    }

    // --- Deal Stages CRUD ---
    public function storeDealStage(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:20',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $v['order'] = \App\Models\DealStage::count() + 1;
        $v['company_id'] = auth()->user()->company_id;

        \App\Models\DealStage::create($v);
        return back()->with('success', __('messages.stage_added') ?? 'Stage added successfully');
    }

    public function updateDealStage(Request $request, \App\Models\DealStage $stage)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:20',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $stage->update($v);
        return back()->with('success', __('messages.stage_updated') ?? 'Stage updated successfully');
    }

    public function destroyDealStage(\App\Models\DealStage $stage)
    {
        if ($stage->deals()->exists()) {
            return back()->with('error', __('messages.cannot_delete_stage_with_deals') ?? 'Cannot delete stage with active deals');
        }
        $stage->delete();
        return back()->with('success', __('messages.stage_deleted') ?? 'Stage deleted successfully');
    }

    public function reorderDealStages(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $index => $id) {
            \App\Models\DealStage::where('id', $id)->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    // --- Loss Reasons CRUD ---
    public function storeLossReason(Request $request)
    {
        $v = $request->validate([
            'reason' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $v['company_id'] = auth()->user()->company_id;
        $v['is_active'] = $request->has('is_active') ? true : false;

        \App\Models\LossReason::create($v);
        return back()->with('success', __('messages.reason_added') ?? 'Reason added successfully');
    }

    public function updateLossReason(Request $request, \App\Models\LossReason $reason)
    {
        $v = $request->validate([
            'reason' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $v['is_active'] = $request->has('is_active') ? true : false;

        $reason->update($v);
        return back()->with('success', __('messages.reason_updated') ?? 'Reason updated successfully');
    }

    public function destroyLossReason(\App\Models\LossReason $reason)
    {
        $reason->delete();
        return back()->with('success', __('messages.reason_deleted') ?? 'Reason deleted successfully');
    }

    public function updateHealthScore(Request $request)
    {
        $data = $request->validate([
            'health_score_new' => 'required|integer|min:0|max:100',
            'health_score_contacted' => 'required|integer|min:0|max:100',
            'health_score_interested' => 'required|integer|min:0|max:100',
            'health_score_converted' => 'required|integer|min:0|max:100',
            'health_score_activity_weight' => 'required|integer|min:0|max:50',
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', __('messages.settings_updated') ?? 'Health score rules updated successfully');
    }

    public function updateWorkflow(Request $request)
    {
        // Checkboxes return strings 'on' or missing.
        SystemSetting::set('lead_dup_name', $request->has('lead_dup_name') ? '1' : '0');
        SystemSetting::set('lead_dup_phone', $request->has('lead_dup_phone') ? '1' : '0');
        SystemSetting::set('lead_dup_email', $request->has('lead_dup_email') ? '1' : '0');

        return back()->with('success', __('messages.settings_updated') ?? 'Workflow settings updated successfully');
    }
}
