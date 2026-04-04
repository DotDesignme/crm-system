<?php
namespace App\Http\Controllers;
use App\Models\{Invoice, InvoiceItem, Deal, Company, Service, Customer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller {
    public function index() {
        $user = Auth::user();
        $q = Invoice::with(['company','deal','createdBy']);
        if(!$user->is_admin) $q->where('company_id',$user->company_id);
        $invoices = $q->latest()->paginate(15);
        $totalOutstanding = (clone $q)->whereNotIn('status',['paid','cancelled'])->sum('total') - (clone $q)->whereNotIn('status',['paid','cancelled'])->sum('paid_amount');
        return view('invoices.index',compact('invoices','totalOutstanding'));
    }
    public function create() {
        $user = Auth::user();
        $deals = $user->is_admin ? Deal::all() : Deal::where('company_id',$user->company_id)->get();
        $companies = $user->is_admin ? Company::all() : Company::where('company_id',$user->company_id)->get();
        $products = $user->is_admin ? Service::all() : Service::where('company_id',$user->company_id)->get();
        $customers = $user->is_admin ? Customer::all() : Customer::where('company_id',$user->company_id)->get();
        $settings = \App\Models\SystemSetting::allCached();
        return view('invoices.create',compact('deals','companies','products', 'customers', 'settings'));
    }
    public function store(Request $r) {
        $user = Auth::user();
        $number = 'INV-'.str_pad(Invoice::count()+1,5,'0',STR_PAD_LEFT);
        $v = $r->validate(['deal_id'=>'nullable','customer_id'=>'nullable','issue_date'=>'nullable|date','due_date'=>'nullable|date','notes'=>'nullable','tax_rate'=>'nullable|numeric','discount_rate'=>'nullable|numeric']);
        
        $v['tax_rate'] = $v['tax_rate'] ?? \App\Models\SystemSetting::get('system_vat_percentage', 14);
        $v['currency'] = \App\Models\SystemSetting::get('system_currency', 'EGP');
        
        $v['invoice_number'] = $number;
        $v['company_id'] = $user->company_id;
        $v['created_by'] = $user->id;
        $items = $r->input('items',[]);
        $subtotal = 0;
        foreach($items as $item) $subtotal += $item['quantity'] * $item['unit_price'];
        $taxAmount = $subtotal * ($v['tax_rate']??0) / 100;
        $discountAmount = $subtotal * ($v['discount_rate']??0) / 100;
        $v['subtotal'] = $subtotal;
        $v['tax_amount'] = $taxAmount;
        $v['discount_amount'] = $discountAmount;
        $v['total'] = $subtotal + $taxAmount - $discountAmount;
        $invoice = Invoice::create($v);
        foreach($items as $item) {
            $item['invoice_id'] = $invoice->id;
            $item['total'] = $item['quantity'] * $item['unit_price'];
            InvoiceItem::create($item);
        }
        return redirect()->route('invoices.index')->with('success',__('messages.invoice_added'));
    }
    public function show(Invoice $invoice) {
        $invoice->load(['items.service','company','deal','createdBy']);
        $settings = \App\Models\SystemSetting::allCached();
        return view('invoices.show',compact('invoice', 'settings'));
    }
    public function recordPayment(Request $r, Invoice $invoice) {
        $r->validate(['amount'=>'required|numeric|min:0']);
        $invoice->update(['paid_amount'=>$invoice->paid_amount + $r->amount]);
        if($invoice->paid_amount >= $invoice->total) $invoice->update(['status'=>'paid']);
        else $invoice->update(['status'=>'partial']);
        return back()->with('success',__('messages.payment_recorded'));
    }
    public function download(Invoice $invoice) {
        $invoice->load(['items.service', 'company', 'deal', 'createdBy', 'page']);
        $system_branding = \App\Models\SystemSetting::allCached();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice', 'system_branding'));
        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function destroy(Invoice $invoice) { $invoice->delete(); return back()->with('success',__('messages.invoice_deleted')); }
}
