<?php
namespace App\Http\Controllers;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $q = Contract::with(['company','deal']);
        if(!$user->is_admin) $q->where('company_id',$user->company_id);
        if($request->filled('status')) $q->where('status',$request->status);
        
        $contracts = $q->latest()->paginate(15)->withQueryString();
        $expiringSoon = (clone $q)->where('status','active')->where('renewal_date','<=',now()->addDays(30))->count();
        
        // Needed for the Create Modal
        $customers = $user->is_admin ? Company::all() : Company::where('id', $user->company_id)->get();
        $deals = $user->is_admin ? \App\Models\Deal::all() : \App\Models\Deal::where('company_id', $user->company_id)->get();
        
        return view('contracts.index',compact('contracts','expiringSoon', 'customers', 'deals'));
    }
    public function store(Request $r) {
        $user = Auth::user();
        $number = 'CON-'.str_pad(Contract::count()+1,5,'0',STR_PAD_LEFT);
        $v = $r->validate(['title'=>'required','customer_id'=>'nullable','deal_id'=>'nullable','value'=>'nullable|numeric','start_date'=>'nullable|date','end_date'=>'nullable|date','auto_renew'=>'nullable','renewal_period_months'=>'nullable|integer','terms'=>'nullable','notes'=>'nullable']);
        $v['contract_number'] = $number;
        $v['company_id'] = $user->company_id;
        $v['created_by'] = $user->id;
        if($v['auto_renew'] && $v['end_date'] && $v['renewal_period_months']) {
            $v['renewal_date'] = $v['end_date'];
        }
        Contract::create($v);

        // Log Activity
        \App\Models\Activity::create([
            'employee_id' => $user->id,
            'type' => 'contract_created',
            'subject' => __('messages.activity_contract_created', ['number' => $number]),
            'description' => __('messages.activity_contract_created_desc', ['title' => $v['title']]),
            'activitiable_id' => $v['customer_id'] ?? null,
            'activitiable_type' => $v['customer_id'] ? \App\Models\Customer::class : null,
            'company_id' => $user->company_id,
        ]);

        return redirect()->route('contracts.index')->with('success',__('messages.contract_added'));
    }
    public function show(Contract $contract) {
        $contract->load(['company','deal','createdBy']);
        return view('contracts.show',compact('contract'));
    }
    public function update(Request $r, Contract $contract) {
        $v = $r->validate(['title'=>'required','status'=>'required','value'=>'nullable|numeric','start_date'=>'nullable|date','end_date'=>'nullable|date','notes'=>'nullable']);
        $contract->update($v);
        return back()->with('success',__('messages.contract_updated'));
    }
    public function destroy(Contract $contract) { 
        $number = $contract->contract_number;
        $contract->delete(); 

        \App\Models\Activity::create([
            'employee_id' => Auth::id(),
            'type' => 'contract_deleted',
            'subject' => __('messages.activity_contract_deleted', ['number' => $number]),
            'description' => 'Contract ' . $number . ' was removed from the system.',
            'company_id' => Auth::user()->company_id,
        ]);

        return back()->with('success',__('messages.contract_deleted')); 
    }
}
