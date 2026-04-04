<?php
namespace App\Http\Controllers;
use App\Models\{Task, Employee};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $q = Task::with(['assignedTo', 'taskable', 'createdBy']);
        if(!$user->is_admin) $q->where('tasks.company_id',$user->company_id);
        if($request->filled('status')) $q->where('status',$request->status);
        if($request->filled('priority')) $q->where('priority',$request->priority);
        if($request->get('mine')) $q->where('assigned_to',$user->id);
        
        $tasks = $q->orderByRaw("CASE WHEN status='pending' THEN 0 WHEN status='in_progress' THEN 1 ELSE 2 END")
                  ->orderBy('due_at')
                  ->paginate(20)
                  ->withQueryString();

        if ($user->is_admin) {
            $pendingCount = Task::where('status','pending')->count();
            $overdueCount = Task::where('status','pending')->where('due_at','<',now())->count();
            $employees = Employee::all();
        } else {
            $pendingCount = Task::where('company_id',$user->company_id)->where('status','pending')->count();
            $overdueCount = Task::where('company_id',$user->company_id)->where('status','pending')->where('due_at','<',now())->count();
            $employees = Employee::where('company_id', $user->company_id)->get();
        }
        
        return view('tasks.index',compact('tasks','pendingCount','overdueCount', 'employees'));
    }

    public function store(Request $r) {
        $user = Auth::user();
        $v = $r->validate([
            'title' => 'required',
            'description' => 'nullable',
            'type' => 'nullable',
            'priority' => 'required',
            'assigned_to' => 'nullable',
            'taskable_type' => 'nullable',
            'taskable_id' => 'nullable',
            'due_at' => 'nullable'
        ]);
        
        $v['company_id'] = $user->company_id;
        $v['created_by'] = $user->id;
        $v['type'] = $v['type'] ?? 'task';
        $v['assigned_to'] = $v['assigned_to'] ?? $user->id;

        $task = Task::create($v);

        \App\Models\Activity::create([
            'employee_id' => $user->id,
            'type' => 'task_created',
            'subject' => 'إضافة مهمة: ' . $task->title,
            'activitiable_type' => Task::class,
            'activitiable_id' => $task->id,
            'company_id' => $user->company_id
        ]);

        return back()->with('success', __('messages.task_added'));
    }

    public function updateStatus(Request $request, Task $task) {
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);
        
        $oldStatus = $task->status;
        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status == 'completed' ? now() : null
        ]);

        if ($oldStatus != $task->status) {
            \App\Models\Activity::create([
                'employee_id' => Auth::id(),
                'type' => 'task_updated',
                'subject' => "تغيير حالة المهمة ($task->title) من " . __("messages.status_$oldStatus") . " إلى " . __("messages.status_{$task->status}"),
                'activitiable_type' => Task::class,
                'activitiable_id' => $task->id,
                'company_id' => $task->company_id
            ]);
        }

        return back()->with('success', __('messages.task_updated'));
    }

    public function complete(Task $task) {
        return $this->updateStatus(new Request(['status' => 'completed']), $task);
    }

    public function destroy(Task $task) { 
        $task->delete(); 
        return back()->with('success',__('messages.task_deleted')); 
    }
}
