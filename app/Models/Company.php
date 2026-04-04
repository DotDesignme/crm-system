<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    protected $fillable = ['name', 'url', 'logo_path', 'brand_color'];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'company_employee');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'page_id');
    }
}
