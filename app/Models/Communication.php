<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
class Communication extends Model {
    protected $fillable = ['type','direction','subject','content','channel','employee_id','communicable_type','communicable_id','communicated_at','company_id'];
    protected $casts = ['communicated_at'=>'datetime'];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function communicable(): MorphTo { return $this->morphTo(); }
    public function customer(): BelongsTo { return $this->belongsTo(Company::class); }
}
