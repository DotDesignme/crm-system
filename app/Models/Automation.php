<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};
class Automation extends Model {
    protected $fillable = ['name','trigger_type','trigger_conditions','actions','is_active','company_id','created_by'];
    protected $casts = ['trigger_conditions'=>'array','actions'=>'array','is_active'=>'boolean'];
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Employee::class,'created_by'); }
}
