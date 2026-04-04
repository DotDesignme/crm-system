<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'company_id', 'employee_id', 'budget', 'total_spend', 'currency',
        'platforms', 'start_date', 'end_date', 'status', 'description',
        'reach', 'impressions', 'clicks', 'conversions', 'leads_generated',
    ];

    protected $casts = [
        'platforms' => 'array',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'page_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function deals()
    {
        return $this->hasManyThrough(Deal::class, Lead::class);
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions == 0) return 0;
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    public function getCpcAttribute(): float
    {
        if ($this->clicks == 0) return 0;
        return round($this->budget / $this->clicks, 2);
    }

    public function getCplAttribute(): float
    {
        $count = $this->leads()->count();
        if ($count == 0) return 0;
        return round($this->total_spend / $count, 2);
    }

    public function getCpqlAttribute(): float
    {
        $count = $this->leads()->where('status', '!=', 'new')->count();
        if ($count == 0) return 0;
        return round($this->total_spend / $count, 2);
    }

    public function getCacAttribute(): float
    {
        $wonCount = $this->deals()->whereHas('stage', function($q) {
            $q->where('is_won', true);
        })->count();
        if ($wonCount == 0) return 0;
        return round($this->total_spend / $wonCount, 2);
    }

    public function getRoiAttribute(): float
    {
        if ($this->total_spend == 0) return 0;
        $revenue = $this->deals()->whereHas('stage', function($q) {
            $q->where('is_won', true);
        })->sum('value');
        
        return round((($revenue - $this->total_spend) / $this->total_spend) * 100, 2);
    }

    public function getPlatformsArAttribute(): string
    {
        $map = [
            'facebook' => 'فيسبوك', 'instagram' => 'إنستجرام',
            'google' => 'جوجل', 'tiktok' => 'تيك توك',
            'youtube' => 'يوتيوب', 'twitter' => 'تويتر',
            'linkedin' => 'لينكد إن', 'snapchat' => 'سناب شات',
        ];
        if (!$this->platforms) return '-';
        return collect($this->platforms)->map(fn($p) => $map[$p] ?? $p)->implode('، ');
    }

    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشطة', 'paused' => 'متوقفة',
            'completed' => 'منتهية', default => $this->status,
        };
    }
}
