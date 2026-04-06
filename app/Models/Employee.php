<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'password', 'company_id', 'is_admin',
        'avatar', 'job_title', 'phone_number', 'status', 
        'email_signature', 'quote_signature', 'is_active', 
        'working_hours', 'notification_preferences'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'working_hours' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_employee');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'added_by');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'assigned_to');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'employee_role');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) return true;
        
        return $this->roles()->whereHas('permissions', function ($q) use ($permission) {
            $q->where('slug', $permission);
        })->exists();
    }

    public function salesTargets(): HasMany
    {
        return $this->hasMany(SalesTarget::class);
    }

    public function activities_log(): HasMany
    {
        return $this->hasMany(EmployeeActivity::class);
    }

    public function currentTarget()
    {
        return $this->salesTargets()
            ->where('month', (int)date('m'))
            ->where('year', (int)date('Y'))
            ->first();
    }
}
