<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'industry', 'website', 'phone', 'email', 'address', 'city', 'country',
        'logo_path', 'annual_revenue', 'employee_count', 'status', 'health_score',
        'assigned_to', 'company_id', 'notes'
    ];

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function notes_list(): \Illuminate\Database\Eloquent\Relations\MorphMany 
    { 
        return $this->morphMany(Note::class, 'noteable'); 
    }

    public function notes_feed(): \Illuminate\Database\Eloquent\Relations\MorphMany 
    { 
        return $this->notes_list(); 
    }
}
