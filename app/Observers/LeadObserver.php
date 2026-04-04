<?php

namespace App\Observers;

use App\Models\Lead;
use App\Services\EmployeeActivityLogger;

class LeadObserver
{
    public function created(Lead $lead): void
    {
        EmployeeActivityLogger::log(
            'lead_added',
            "New lead added: {$lead->name}",
            ['lead_id' => $lead->id]
        );
    }

    public function updated(Lead $lead): void
    {
        if ($lead->wasChanged('status')) {
            EmployeeActivityLogger::log(
                'lead_status_changed',
                "Lead status changed to " . ucfirst($lead->status) . " for {$lead->name}",
                [
                    'lead_id' => $lead->id,
                    'old_status' => $lead->getOriginal('status'),
                    'new_status' => $lead->status
                ]
            );
        }
        
        if ($lead->wasChanged('added_by')) {
            EmployeeActivityLogger::log(
                'lead_assigned',
                "Lead {$lead->name} assigned to work",
                [
                    'lead_id' => $lead->id,
                    'assigned_to' => $lead->added_by
                ]
            );
        }
    }
}
