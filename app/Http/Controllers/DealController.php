<?php
namespace App\Http\Controllers;
use App\Models\{Deal, DealStage, Company, Employee, Lead};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $view = $request->get('view','kanban');
        $stages = DealStage::with(['deals'=>function($q)use($user){
            if(!$user->is_admin) $q->where('deals.company_id',$user->company_id);
            $q->with(['company','assignedTo','lead']);
        }])->orderBy('order')->get();
        $customers = $user->is_admin ? Company::all() : Company::where('id',$user->company_id)->get();
        $employees = $user->is_admin ? Employee::all() : Employee::where('company_id',$user->company_id)->get();
        $totalValue = $stages->sum(fn($s)=>$s->deals->sum('value'));
        return view('deals.index',compact('stages','customers','employees','totalValue','view'));
    }
    public function create() { return redirect()->route('deals.index'); }
    public function store(Request $r) {
        $user = Auth::user();
        $v = $r->validate(['title'=>'required','value'=>'nullable|numeric','customer_id'=>'nullable','lead_id'=>'nullable','deal_stage_id'=>'required','expected_close_date'=>'nullable|date','probability'=>'nullable|integer','description'=>'nullable','source'=>'nullable']);
        $v['company_id'] = $user->company_id;
        $v['assigned_to'] = $v['assigned_to'] ?? $user->id;
        $v['value'] = $v['value'] ?? 0;
        $deal = Deal::create($v);
        \App\Models\Activity::create(['employee_id'=>$user->id,'type'=>'deal_created','subject'=>'تم إنشاء صفقة: '.$deal->title,'activitiable_type'=>Deal::class,'activitiable_id'=>$deal->id,'company_id'=>$user->company_id]);
        return redirect()->route('deals.index')->with('success',__('messages.deal_added'));
    }
    public function show(Deal $deal) {
        $deal->load(['company','lead','stage','assignedTo','quotations','invoices','tasks.assignedTo','notes_list.employee','communications','activities.employee']);
        $lossReasons = \App\Models\LossReason::where('is_active', true)->where('company_id', $deal->company_id)->get();
        return view('deals.show',compact('deal', 'lossReasons'));
    }
    public function edit(Deal $deal) {
        $user = Auth::user();
        $stages = DealStage::where('company_id', $deal->company_id)->orderBy('order')->get();
        $companies = $user->is_admin ? Company::all() : Company::where('company_id', $user->company_id)->get();
        $employees = $user->is_admin ? Employee::all() : Employee::where('company_id', $user->company_id)->get();
        return view('deals.edit', compact('deal', 'stages', 'companies', 'employees'));
    }
    public function update(Request $r, Deal $deal) {
        $oldStage = $deal->deal_stage_id;
        $v = $r->validate([
            'title'=>'required',
            'value'=>'nullable|numeric',
            'customer_id'=>'nullable',
            'deal_stage_id'=>'required',
            'expected_close_date'=>'nullable|date',
            'probability'=>'nullable|integer',
            'description'=>'nullable',
            'source'=>'nullable',
            'loss_reason_id'=>'nullable|exists:loss_reasons,id',
            'loss_notes'=>'nullable'
        ]);
        
        $deal->update($v);

        if($oldStage != $v['deal_stage_id']) {
            $stage = DealStage::find($v['deal_stage_id']);
            \App\Models\Activity::create([
                'employee_id'=>Auth::id(),
                'type'=>'deal_moved',
                'subject'=>"نقل الصفقة إلى: ".$stage->name,
                'activitiable_type'=>Deal::class,
                'activitiable_id'=>$deal->id,
                'company_id'=>$deal->company_id
            ]);
        }
        return back()->with('success',__('messages.deal_updated'));
    }
    public function move(Request $r, Deal $deal) {
        $deal->update(['deal_stage_id'=>$r->deal_stage_id]);
        return response()->json(['success'=>true]);
    }
    public function destroy(Deal $deal) { $deal->delete(); return back()->with('success',__('messages.deal_deleted')); }
}
