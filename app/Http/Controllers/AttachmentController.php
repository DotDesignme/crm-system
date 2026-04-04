<?php

namespace App\Http\Controllers;

use App\Models\{Attachment, Activity};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB limit
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('attachments', 'public');

            $attachment = Attachment::create([
                'employee_id' => $user->id,
                'company_id' => $user->company_id,
                'attachable_type' => $request->attachable_type,
                'attachable_id' => $request->attachable_id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);


            Activity::create([
                'employee_id' => $user->id,
                'company_id' => $user->company_id,
                'type' => 'file_added',
                'subject' => __('messages.file_added') . ': ' . $file->getClientOriginalName(),
                'activitiable_type' => $request->attachable_type,
                'activitiable_id' => $request->attachable_id,
            ]);

            return back()->with('success', __('messages.file_added'));
        }

        return back()->withErrors(['file' => __('messages.file_upload_error')]);
    }

    public function download(Attachment $attachment)
    {
        $user = Auth::user();
        if (!$user->is_admin && $attachment->company_id !== $user->company_id) {
            abort(403);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroy(Attachment $attachment)
    {
        $user = Auth::user();
        if (!$user->is_admin && $attachment->employee_id !== $user->id) {
            abort(403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', __('messages.file_deleted'));
    }
}
