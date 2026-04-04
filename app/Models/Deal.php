<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, MorphMany};

class Deal extends Model
{
    protected $fillable = [
        'title', 'deal_type', 'value', 'currency', 'customer_id', 'lead_id', 
        'deal_stage_id', 'assigned_to', 'company_id', 'expected_close_date', 
        'actual_close_date', 'probability', 'description', 'loss_reason', 
        'loss_reason_id', 'loss_notes', 'win_reason', 'win_notes', 'source'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'weighted_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'probability' => 'integer'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DealStage::class, 'deal_stage_id');
    }

    public function lossReason(): BelongsTo
    {
        return $this->belongsTo(LossReason::class, 'loss_reason_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activitiable');
    }

    public function notes_list(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
