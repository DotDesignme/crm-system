<?php
namespace App\Http\Controllers;
use App\Models\{Note, Activity};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller {
    public function store(Request $r) {
        $user = Auth::user();
        $v = $r->validate(['content'=>'required','noteable_type'=>'required','noteable_id'=>'required']);
        $v['employee_id'] = $user->id;
        $v['company_id'] = $user->company_id;
        Note::create($v);
        Activity::create(['employee_id'=>$user->id,'type'=>'note_added','subject'=>'إضافة ملاحظة','description'=>substr($v['content'],0,100),'activitiable_type'=>$v['noteable_type'],'activitiable_id'=>$v['noteable_id'],'company_id'=>$user->company_id]);
        return back()->with('success',__('messages.note_added'));
    }

    public function destroy(Note $note) {
        if ($note->employee_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        // Optional: delete related activity
        Activity::where('activitiable_type', get_class($note->noteable))
                ->where('activitiable_id', $note->noteable_id)
                ->where('type', 'note_added')
                ->where('employee_id', $note->employee_id)
                ->latest()
                ->first()?->delete();

        $note->delete();
        return back()->with('success', __('messages.note_deleted'));
    }
}
