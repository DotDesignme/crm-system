<?php

namespace App\Http\Controllers;

use App\Models\{Customer, Employee};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $q = Customer::with(['assignedEmployee', 'contacts', 'deals']);
        
        if (!$user->is_admin) {
            $q->where('company_id', $user->company_id);
        }
        
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(fn($w) => $w->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%"));
        }
        
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        
        $customers = $q->latest()->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return redirect()->route('customers.index');
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            'name' => 'required',
            'industry' => 'nullable',
            'website' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'status' => 'required',
            'notes' => 'nullable'
        ]);
        
        $v['company_id'] = Auth::user()->company_id;
        $v['assigned_to'] = Auth::id();
        
        Customer::create($v);
        return redirect()->route('customers.index')->with('success', __('messages.customer_added'));
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        if (!$user->is_admin && $customer->company_id !== $user->company_id) {
            abort(403);
        }

        $customer->load(['contacts', 'deals.stage', 'leads', 'contracts', 'assignedEmployee']);
        
        $directActivities = \App\Models\Activity::where('activitiable_id', $customer->id)
            ->where('activitiable_type', Customer::class)
            ->get();
            
        $dealActivities = $customer->deals->flatMap->activities;
        
        $activities = $directActivities->concat($dealActivities)
            ->sortByDesc('created_at')
            ->take(20);
            
        return view('customers.show', compact('customer', 'activities'));
    }

    public function update(Request $r, Customer $customer)
    {
        $user = Auth::user();
        if (!$user->is_admin && ($customer->company_id !== $user->company_id || $customer->assigned_to !== $user->id)) {
            abort(403);
        }

        $v = $r->validate([
            'name' => 'required',
            'industry' => 'nullable',
            'website' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'status' => 'required',
            'health_score' => 'nullable',
            'notes' => 'nullable'
        ]);
        
        $customer->update($v);
        return back()->with('success', __('messages.customer_updated'));
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        if (!$user->is_admin && ($customer->company_id !== $user->company_id || $customer->assigned_to !== $user->id)) {
            abort(403);
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', __('messages.customer_deleted'));
    }
}
