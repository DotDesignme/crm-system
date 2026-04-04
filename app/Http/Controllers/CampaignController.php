<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Campaign::with(['company', 'employee']);

        if (!$user->is_admin) {
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('name', 'like', "%{$s}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'total_spend' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'platforms' => 'required|array|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paused,completed',
            'description' => 'nullable|string',
            'reach' => 'nullable|integer|min:0',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['employee_id'] = $user->id;

        Campaign::create($validated);
        return redirect()->route('campaigns.index')->with('success', __('messages.campaign_added'));
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['company', 'employee']);
        
        $leads = $campaign->leads()->latest()->get();
        $wonDeals = $campaign->deals()->whereHas('stage', function($q) {
            $q->where('is_won', true);
        })->latest()->get();

        // Funnel Data
        $funnel = [
            'total' => $campaign->leads()->count(),
            'contacted' => $campaign->leads()->whereIn('status', ['contacted', 'interested', 'converted'])->count(),
            'interested' => $campaign->leads()->whereIn('status', ['interested', 'converted'])->count(),
            'won' => $wonDeals->count()
        ];

        return view('campaigns.show', compact('campaign', 'leads', 'wonDeals', 'funnel'));
    }

    public function edit(Campaign $campaign)
    {
        $user = Auth::user();
        if (!$user->is_admin && $campaign->company_id !== $user->company_id) abort(403);
        return view('campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $user = Auth::user();
        if (!$user->is_admin && $campaign->company_id !== $user->company_id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'total_spend' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'platforms' => 'required|array|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paused,completed',
            'description' => 'nullable|string',
            'reach' => 'nullable|integer|min:0',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
        ]);

        $campaign->update($validated);
        return redirect()->route('campaigns.index')->with('success', __('messages.campaign_updated'));
    }

    public function destroy(Campaign $campaign)
    {
        $user = Auth::user();
        if (!$user->is_admin && $campaign->employee_id !== $user->id) abort(403);
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', __('messages.campaign_deleted'));
    }
}
