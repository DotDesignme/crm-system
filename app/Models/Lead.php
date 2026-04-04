<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany};

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'notes', 'status', 'priority',
        'source', 'follow_up_at', 'tag', 'company_id', 'added_by', 'campaign_id', 'customer_id',
    ];

    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'new' => __('messages.status_new'),
            'contacted' => __('messages.status_contacted'),
            'interested' => __('messages.status_interested'),
            'not_interested' => __('messages.status_not_interested'),
            'converted' => __('messages.status_converted'),
            default => $this->status,
        };
    }

    public function getPriorityArAttribute(): string
    {
        return match($this->priority) {
            'low' => __('messages.priority_low'),
            'medium' => __('messages.priority_medium'),
            'high' => __('messages.priority_high'),
            'urgent' => __('messages.priority_urgent'),
            default => $this->priority ?? '-',
        };
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'added_by');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
    public function tasks(): MorphMany { return $this->morphMany(Task::class,'taskable'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class,'activitiable'); }
    public function notes_list(): MorphMany { return $this->morphMany(Note::class,'noteable'); }
    public function notes(): MorphMany { return $this->notes_list(); }
    public function communications(): MorphMany { return $this->morphMany(Communication::class,'communicable'); }
    public function attachments(): MorphMany { return $this->morphMany(Attachment::class,'attachable'); }
    public function getScoreAttribute(): int {
        $settings = SystemSetting::allCached();
        $base = match($this->status) {
            'new' => (int)($settings['health_score_new'] ?? 10),
            'contacted' => (int)($settings['health_score_contacted'] ?? 30),
            'interested' => (int)($settings['health_score_interested'] ?? 60),
            'converted' => (int)($settings['health_score_converted'] ?? 100),
            default => 0
        };
        $weight = (int)($settings['health_score_activity_weight'] ?? 5);
        $engagement = $this->activities()->count() * $weight;
        return min(100, $base + $engagement);
    }

    public static function getNextAgentId($companyId)
    {
        $agents = Employee::whereHas('companies', function($q) use ($companyId) {
                $q->where('companies.id', $companyId);
            })
            ->where('is_admin', false)
            ->orderBy('id', 'asc')
            ->get();

        if ($agents->isEmpty()) {
            return \Illuminate\Support\Facades\Auth::id();
        }

        $lastLead = self::where('company_id', $companyId)
            ->whereNotNull('added_by')
            ->latest('id')
            ->first();

        if (!$lastLead) {
            return $agents->first()->id;
        }

        $lastAgentId = $lastLead->added_by;
        $currentIndex = $agents->search(function ($agent) use ($lastAgentId) {
            return $agent->id == $lastAgentId;
        });

        if ($currentIndex === false || $currentIndex === $agents->count() - 1) {
            return $agents->first()->id;
        }

        return $agents[$currentIndex + 1]->id;
    }
}
