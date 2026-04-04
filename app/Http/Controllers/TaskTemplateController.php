<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\TaskTemplate;
use App\Models\TaskTemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskTemplateController extends Controller
{
    public function index()
    {
        $templates = TaskTemplate::where('company_id', Auth::user()->company_id)
            ->withCount('items')
            ->get();
            
        return view('task_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('task_templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.delay_days' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $template = TaskTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'company_id' => Auth::user()->company_id,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $template->items()->create($item);
            }
        });

        return redirect()->route('task-templates.index')
            ->with('success', __('messages.template_added'));
    }

    public function edit(TaskTemplate $template)
    {
        $template->load('items');
        return view('task_templates.edit', compact('template'));
    }

    public function update(Request $request, TaskTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $template) {
            $template->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $template->items()->delete();
            foreach ($request->items as $item) {
                $template->items()->create($item);
            }
        });

        return redirect()->route('task-templates.index')
            ->with('success', __('messages.template_updated'));
    }

    public function destroy(TaskTemplate $template)
    {
        $template->delete();
        return redirect()->route('task-templates.index')
            ->with('success', __('messages.template_deleted'));
    }

    /**
     * Preview template tasks for a lead.
     */
    public function preview(Request $request, Lead $lead, TaskTemplate $template)
    {
        // Ensure same company
        if ($template->company_id !== $lead->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $preview = $template->items->map(function ($item) {
            return [
                'title' => $item->title,
                'description' => $item->description,
                'type' => $item->type,
                'priority' => $item->priority,
                'due_at' => \Illuminate\Support\Carbon::now()
                    ->addDays($item->delay_days)
                    ->addHours($item->delay_hours)
                    ->format('Y-m-d\TH:i'),
            ];
        });

        return response()->json([
            'template' => $template->name,
            'items' => $preview,
            'employees' => $lead->company->employees->map->only(['id', 'name']),
        ]);
    }

    /**
     * Apply template with custom data to a lead.
     */
    public function apply(Request $request, Lead $lead)
    {
        $request->validate([
            'template_id' => 'required|exists:task_templates,id',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.due_at' => 'required|date',
        ]);

        $template = TaskTemplate::findOrFail($request->template_id);
        
        // Ensure same company
        if ($template->company_id !== $lead->company_id) {
            abort(403);
        }

        DB::transaction(function () use ($request, $lead, $template) {
            foreach ($request->items as $item) {
                \App\Models\Task::create([
                    'title' => $item['title'],
                    'description' => $item['description'] ?? null,
                    'type' => $item['type'] ?? 'follow_up',
                    'priority' => $item['priority'] ?? 'medium',
                    'status' => 'pending',
                    'assigned_to' => $item['assigned_to'] ?? Auth::id(),
                    'created_by' => Auth::id(),
                    'taskable_type' => Lead::class,
                    'taskable_id' => $lead->id,
                    'due_at' => $item['due_at'],
                    'company_id' => $lead->company_id,
                ]);
            }

            // Log the activity
            \App\Models\Activity::create([
                'employee_id' => Auth::id(),
                'type' => 'template_applied',
                'subject' => __('messages.template_applied_to', ['template' => $template->name]),
                'description' => __('messages.template_applied_desc', ['count' => count($request->items)]),
                'activitiable_type' => Lead::class,
                'activitiable_id' => $lead->id,
                'company_id' => $lead->company_id,
            ]);
        });

        return response()->json(['success' => true, 'message' => __('messages.template_applied')]);
    }
}
