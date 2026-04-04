<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTarget extends Model
{
    protected $fillable = [
        'employee_id',
        'target_amount',
        'commission_percentage',
        'month',
        'year',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
