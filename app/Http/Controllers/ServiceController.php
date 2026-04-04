<?php
namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $q = Service::with('pricings');
        if(!$user->is_admin) $q->where('company_id',$user->company_id);
        if($request->filled('search')) $q->where('name','like','%'.$request->search.'%');
        $services = $q->latest()->paginate(15)->withQueryString();
        return view('services.index', compact('services'));
    }
    public function store(Request $r) {
        $v = $r->validate(['name'=>'required','sku'=>'nullable','description'=>'nullable','category'=>'nullable','cost_price'=>'nullable|numeric','selling_price'=>'required|numeric','unit'=>'nullable','is_service'=>'nullable']);
        $v['company_id'] = Auth::user()->company_id;
        $v['is_service'] = true;
        Service::create($v);
        return redirect()->route('services.index')->with('success', __('messages.service_added'));
    }
    public function update(Request $r, Service $service) {
        $v = $r->validate(['name'=>'required','sku'=>'nullable','description'=>'nullable','category'=>'nullable','cost_price'=>'nullable|numeric','selling_price'=>'required|numeric','unit'=>'nullable','is_service'=>'nullable','is_active'=>'nullable']);
        $service->update($v);
        return back()->with('success', __('messages.service_updated'));
    }
    public function destroy(Service $service) { $service->delete(); return back()->with('success', __('messages.service_deleted')); }
}
