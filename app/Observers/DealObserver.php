<?php

namespace App\Observers;

use App\Models\Deal;
use App\Services\EmployeeActivityLogger;

class DealObserver
{
    public function updated(Deal $deal): void
    {
        if ($deal->wasChanged('deal_stage_id')) {
            $newStage = $deal->stage;
            
            if ($newStage->is_won) {
                EmployeeActivityLogger::log(
                    'deal_won',
                    "Deal won: {$deal->title} - Value: " . number_format((float)$deal->value, 2),
                    [
                        'deal_id' => $deal->id,
                        'value' => (float)$deal->value,
                        'revenue' => (float)$deal->value
                    ]
                );
            } elseif ($newStage->is_lost) {
                EmployeeActivityLogger::log(
                    'deal_lost',
                    "Deal lost: {$deal->title}",
                    [
                        'deal_id' => $deal->id,
                        'loss_reason_id' => $deal->loss_reason_id,
                        'loss_notes' => $deal->loss_notes
                    ]
                );
            }
        }
    }
}
