<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\EmployeeActivityLogger;

class TaskObserver
{
    public function created(Task $task): void
    {
        if ($task->type === 'meeting') {
            EmployeeActivityLogger::log(
                'meeting_scheduled',
                "Meeting scheduled with lead #{$task->lead_id} on " . $task->due_at->format('Y-m-d H:i'),
                [
                    'task_id' => $task->id,
                    'lead_id' => $task->lead_id,
                    'due_at' => $task->due_at
                ]
            );
        } else {
            EmployeeActivityLogger::log(
                'task_assigned',
                "New task assigned: {$task->title}",
                [
                    'task_id' => $task->id,
                    'type' => $task->type
                ]
            );
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('status') && $task->status === 'completed') {
            EmployeeActivityLogger::log(
                'task_completed',
                "Task completed: {$task->title}",
                [
                    'task_id' => $task->id,
                    'type' => $task->type
                ]
            );
        }
    }
}
