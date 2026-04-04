<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CompanyContact;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerContactController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'role' => 'required|string',
            'is_decision_maker' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['customer_id'] = $customer->id;
        $validated['is_decision_maker'] = $request->has('is_decision_maker');

        $contact = \App\Models\CustomerContact::create($validated);

        // Log Activity
        Activity::create([
            'employee_id' => Auth::id(),
            'type' => 'contact_added',
            'subject' => __('messages.activity_contact_added', ['name' => $contact->name]),
            'description' => __('messages.activity_contact_added_desc', ['customer' => $customer->name]),
            'activitiable_id' => $customer->id,
            'activitiable_type' => Customer::class,
            'company_id' => Auth::user()->company_id,
        ]);

        return back()->with('success', __('messages.contact_added_success'));
    }

    public function destroy(\App\Models\CustomerContact $contact)
    {
        $contact->delete();
        return back()->with('success', __('messages.contact_deleted_success'));
    }
}
