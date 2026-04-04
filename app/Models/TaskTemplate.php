<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskTemplate extends Model
{
    protected $fillable = ['name', 'description', 'company_id', 'created_by'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TaskTemplateItem::class);
    }

    /**
     * Apply this template to a lead or deal.
     *
     * @param Model $taskable
     * @param int|null $assignedTo
     * @return void
     */
    public function applyTo(Model $taskable, $assignedTo = null)
    {
        foreach ($this->items as $item) {
            $dueAt = Carbon::now()
                ->addDays($item->delay_days)
                ->addHours($item->delay_hours);

            Task::create([
                'title' => $item->title,
                'description' => $item->description,
                'type' => $item->type,
                'priority' => $item->priority,
                'status' => 'pending',
                'assigned_to' => $assignedTo ?? Auth::id(),
                'created_by' => Auth::id(),
                'taskable_type' => get_class($taskable),
                'taskable_id' => $taskable->getKey(),
                'due_at' => $dueAt,
                'company_id' => $this->company_id,
            ]);
        }

        // Log the activity
        Activity::create([
            'employee_id' => Auth::id(),
            'type' => 'template_applied',
            'subject' => __('messages.template_applied_to', ['template' => $this->name]),
            'description' => __('messages.template_applied_desc', ['count' => $this->items->count()]),
            'activitiable_type' => get_class($taskable),
            'activitiable_id' => $taskable->getKey(),
            'company_id' => $this->company_id,
        ]);
    }
}
