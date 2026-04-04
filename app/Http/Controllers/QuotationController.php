<?php
namespace App\Http\Controllers;
use App\Models\{Quotation, QuotationItem, Deal, Company, Service, Customer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller {
    public function index() {
        $user = Auth::user();
        $q = Quotation::with(['company','deal','createdBy']);
        if(!$user->is_admin) $q->where('company_id',$user->company_id);
        $quotations = $q->latest()->paginate(15);
        return view('quotations.index',compact('quotations'));
    }
    public function create() {
        $user = Auth::user();
        $deals = $user->is_admin ? Deal::all() : Deal::where('company_id',$user->company_id)->get();
        $companies = $user->is_admin ? Company::all() : Company::where('company_id',$user->company_id)->get();
        $products = $user->is_admin ? Service::all() : Service::where('company_id',$user->company_id)->get();
        $customers = $user->is_admin ? Customer::all() : Customer::where('company_id',$user->company_id)->get();
        $settings = \App\Models\SystemSetting::allCached();
        return view('quotations.create',compact('deals','companies','products', 'customers', 'settings'));
    }
    public function store(Request $r) {
        $user = Auth::user();
        $number = 'QT-'.str_pad(Quotation::count()+1,5,'0',STR_PAD_LEFT);
        $v = $r->validate(['deal_id'=>'nullable','customer_id'=>'nullable','notes'=>'nullable','terms'=>'nullable','valid_until'=>'nullable|date','tax_rate'=>'nullable|numeric','discount_rate'=>'nullable|numeric']);
        
        $v['tax_rate'] = $v['tax_rate'] ?? \App\Models\SystemSetting::get('system_vat_percentage', 14);
        $v['currency'] = \App\Models\SystemSetting::get('system_currency', 'EGP');
        
        $v['quotation_number'] = $number;
        $v['company_id'] = $user->company_id;
        $v['created_by'] = $user->id;
        $items = $r->input('items',[]);
        $subtotal = 0;
        foreach($items as $item) $subtotal += ($item['quantity'] * $item['unit_price']) - ($item['discount']??0);
        $taxAmount = $subtotal * ($v['tax_rate']??0) / 100;
        $discountAmount = $subtotal * ($v['discount_rate']??0) / 100;
        $v['subtotal'] = $subtotal;
        $v['tax_amount'] = $taxAmount;
        $v['discount_amount'] = $discountAmount;
        $v['total'] = $subtotal + $taxAmount - $discountAmount;
        $quotation = Quotation::create($v);
        foreach($items as $item) {
            $item['quotation_id'] = $quotation->id;
            $item['total'] = ($item['quantity'] * $item['unit_price']) - ($item['discount']??0);
            QuotationItem::create($item);
        }
        return redirect()->route('quotations.index')->with('success',__('messages.quotation_added'));
    }
    public function show(Quotation $quotation) {
        $quotation->load(['items.service','company','deal','contact','createdBy']);
        $settings = \App\Models\SystemSetting::allCached();
        return view('quotations.show',compact('quotation', 'settings'));
    }
    public function download(Quotation $quotation) {
        $quotation->load(['items.service','company','deal','contact','createdBy','page']);
        $system_branding = \App\Models\SystemSetting::allCached();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotations.pdf', compact('quotation', 'system_branding'));
        return $pdf->download('Quotation-'.$quotation->quotation_number.'.pdf');
    }
    public function destroy(Quotation $quotation) { $quotation->delete(); return back()->with('success',__('messages.quotation_deleted')); }

    public function convertToInvoice(Quotation $quotation) {
        $user = Auth::user();
        
        // 1. Create Invoice
        $invoiceNumber = 'INV-' . str_pad(\App\Models\Invoice::count() + 1, 5, '0', STR_PAD_LEFT);
        $invoice = \App\Models\Invoice::create([
            'invoice_number' => $invoiceNumber,
            'deal_id' => $quotation->deal_id,
            'customer_id' => $quotation->customer_id,
            'company_id' => $quotation->company_id,
            'created_by' => $user->id,
            'subtotal' => $quotation->subtotal,
            'tax_rate' => $quotation->tax_rate,
            'tax_amount' => $quotation->tax_amount,
            'discount_rate' => $quotation->discount_rate,
            'discount_amount' => $quotation->discount_amount,
            'total' => $quotation->total,
            'currency' => $quotation->currency ?? \App\Models\SystemSetting::get('system_currency', 'EGP'),
            'status' => 'unpaid',
            'due_date' => now()->addDays(14),
            'notes' => $quotation->notes,
            'terms' => $quotation->terms,
        ]);

        // 2. Map Items
        foreach ($quotation->items as $item) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $item->service_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'tax' => $item->tax,
                'total' => $item->total,
            ]);
        }

        // 3. Update Quotation Status
        $quotation->update(['status' => 'accepted']);

        // 4. Record Activity
        \App\Models\Activity::create([
            'employee_id' => $user->id,
            'company_id' => $user->company_id,
            'type' => 'invoice_created',
            'subject' => __('messages.invoice_generated_from_quotation') . " #{$quotation->quotation_number}",
            'description' => __('messages.invoice_number') . ": {$invoiceNumber}",
            'activitiable_type' => 'App\Models\Deal',
            'activitiable_id' => $quotation->deal_id,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', __('messages.invoice_created_success'));
    }
}
