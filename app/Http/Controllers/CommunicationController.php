<?php
namespace App\Http\Controllers;
use App\Models\{Communication, Lead, Deal, Company};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller {
    public function store(Request $r) {
        $user = Auth::user();
        $type = $r->input('communication_type', $r->input('type', 'call'));
        $v = $r->validate([
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'communicable_type' => 'required|string',
            'communicable_id' => 'required|integer',
            'metadata' => 'nullable|array'
        ]);

        $comm = Communication::create([
            'employee_id' => $user->id,
            'company_id' => $user->company_id,
            'type' => $type,
            'subject' => $v['subject'] ?? ucfirst($type),
            'content' => $v['content'],
            'communicable_type' => $v['communicable_type'],
            'communicable_id' => $v['communicable_id'],
            'metadata' => $v['metadata'] ?? [],
            'communicated_at' => now(),
        ]);

        \App\Models\Activity::create([
            'employee_id' => $user->id,
            'company_id' => $user->company_id,
            'type' => $type,
            'subject' => ($v['subject'] ?? ucfirst($type)) . " (" . __('messages.type_' . $type) . ")",
            'description' => substr($v['content'], 0, 150),
            'activitiable_type' => $v['communicable_type'],
            'activitiable_id' => $v['communicable_id'],
            'metadata' => ['communication_id' => $comm->id]
        ]);

        return back()->with('success', __('messages.comm_added'));
    }

    public function logWhatsAppStore(Request $request) {
        $v = $request->validate([
            'communicable_type' => 'required',
            'communicable_id' => 'required',
            'phone' => 'required'
        ]);

        $comm = Communication::create([
            'employee_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'communicable_type' => $v['communicable_type'],
            'communicable_id' => $v['communicable_id'],
            'type' => 'whatsapp',
            'subject' => 'بدء محادثة واتساب',
            'content' => 'تم النقر على رابط الواتساب للرقم: ' . $v['phone'],
            'communicated_at' => now(),
            'metadata' => ['phone' => $v['phone']]
        ]);

        \App\Models\Activity::create([
            'employee_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'type' => 'whatsapp',
            'subject' => 'بدء محادثة واتساب (' . $v['phone'] . ')',
            'activitiable_type' => $v['communicable_type'],
            'activitiable_id' => $v['communicable_id'],
            'metadata' => ['communication_id' => $comm->id]
        ]);

        return response()->json(['success' => true]);
    }
}
