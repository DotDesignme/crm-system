<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
class Note extends Model {
    protected $fillable = ['content','employee_id','noteable_type','noteable_id','company_id'];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function noteable(): MorphTo { return $this->morphTo(); }
    public function customer(): BelongsTo { return $this->belongsTo(Company::class); }
}
