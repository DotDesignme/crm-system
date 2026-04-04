<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function kanban()
    {
        $user = Auth::user();
        $query = Lead::with(['company', 'employee']);
        if (!$user->is_admin) {
            $query->where('company_id', $user->company_id);
        }
        $leads = $query->get()->groupBy('status');
        $statuses = ['new', 'contacted', 'interested', 'not_interested', 'converted'];
        return view('leads.kanban', compact('leads', 'statuses'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|in:new,contacted,interested,not_interested,converted'
        ]);

        $lead = Lead::findOrFail($request->lead_id);
        $user = Auth::user();
        if (!$user->is_admin && $lead->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lead->status = $request->status;
        $lead->save();

        \App\Models\Activity::create([
            'employee_id' => $user->id,
            'type' => 'lead_status_updated',
            'subject' => __('messages.lead_moved', ['status' => __('messages.status_' . $request->status)]),
            'activitiable_type' => Lead::class,
            'activitiable_id' => $lead->id,
            'company_id' => $lead->company_id
        ]);

        return response()->json(['success' => true]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['company', 'employee']);

        if (!$user->is_admin) {
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();
        $campaigns = Campaign::where('company_id', $user->company_id)->get();

        return view('leads.index', compact('leads', 'campaigns'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $companies = \App\Models\Company::all();
            $campaigns = Campaign::all();
            $employees = \App\Models\Employee::where('is_admin', false)->get();
        } else {
            $companies = collect();
            $campaigns = Campaign::where('company_id', $user->company_id)->get();
            $employees = \App\Models\Employee::where('company_id', $user->company_id)->where('is_admin', false)->get();
        }
        return view('leads.create', compact('campaigns', 'companies', 'employees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,interested,not_interested,converted',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'source' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'follow_up_at' => 'nullable|date',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ], [
            'name.required' => __('messages.required'),
            'email.email' => __('messages.invalid_email'),
        ]);

        $checkName = \App\Models\SystemSetting::get('lead_dup_name', '0') === '1';
        $checkPhone = \App\Models\SystemSetting::get('lead_dup_phone', '1') === '1';
        $checkEmail = \App\Models\SystemSetting::get('lead_dup_email', '0') === '1';

        $hasConstraints = false;
        if ($checkName && !empty($validated['name'])) $hasConstraints = true;
        if ($checkPhone && !empty($validated['phone'])) $hasConstraints = true;
        if ($checkEmail && !empty($validated['email'])) $hasConstraints = true;

        if ($hasConstraints) {
            $duplicateQuery = Lead::query();
            $duplicateQuery->where(function ($q) use ($validated, $checkName, $checkPhone, $checkEmail) {
                $first = true;
                if ($checkName && !empty($validated['name'])) {
                    $q->where('name', $validated['name']);
                    $first = false;
                }
                if ($checkPhone && !empty($validated['phone'])) {
                    $method = $first ? 'where' : 'orWhere';
                    $q->$method('phone', $validated['phone']);
                    $first = false;
                }
                if ($checkEmail && !empty($validated['email'])) {
                    $method = $first ? 'where' : 'orWhere';
                    $q->$method('email', $validated['email']);
                }
            });

            $existing = $duplicateQuery->first();
            if ($existing) {
                $owner = $existing->employee;
                return back()->withErrors([
                    'phone' => __('messages.duplicate_lead', [
                        'employee' => $owner ? $owner->name : 'Unknown', 
                        'date' => $existing->created_at ? $existing->created_at->format('Y-m-d') : 'Unknown Date'
                    ]),
                ])->withInput();
            }
        }

        if ($user->is_admin && $request->filled('company_id')) {
            $validated['company_id'] = $request->company_id;
        } else {
            $validated['company_id'] = $user->company_id;
        }
        
        if ($user->is_admin && $request->filled('added_by')) {
            $validated['added_by'] = $request->added_by;
        } else {
            $validated['added_by'] = $user->id;
        }

        Lead::create($validated);

        return redirect()->route('leads.index')->with('success', __('messages.lead_added'));
    }

    public function show(Lead $lead)
    {
        $user = Auth::user();
        if (!$user->is_admin && $lead->company_id !== $user->company_id) {
            abort(403);
        }

        $lead->load(['company', 'employee', 'activities.employee', 'notes_list.employee', 'communications.employee', 'tasks.assignedTo']);
        
        $templates = TaskTemplate::where('company_id', $user->company_id)->get();
        
        return view('leads.show', compact('lead', 'templates'));
    }

    public function edit(Lead $lead)
    {
        $user = Auth::user();
        if (!$user->is_admin && $lead->company_id !== $user->company_id) {
            abort(403);
        }

        $campaigns = Campaign::where('company_id', $user->company_id)->get();
        return view('leads.edit', compact('lead', 'campaigns'));
    }

    public function update(Request $request, Lead $lead)
    {
        $user = Auth::user();
        if (!$user->is_admin && $lead->company_id !== $user->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,contacted,interested,not_interested,converted',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'source' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'follow_up_at' => 'nullable|date',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.index')->with('success', __('messages.lead_updated'));
    }

    public function destroy(Lead $lead)
    {
        $user = Auth::user();
        if (!$user->is_admin && $lead->added_by !== $user->id) {
            abort(403);
        }

        $lead->delete();

        return redirect()->route('leads.index')->with('success', __('messages.lead_deleted'));
    }
}
