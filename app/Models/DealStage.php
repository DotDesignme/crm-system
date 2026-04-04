<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class DealStage extends Model {
    protected $fillable = ['name','color','order','company_id','is_won','is_lost'];
    protected $casts = ['is_won'=>'boolean','is_lost'=>'boolean'];
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function deals(): HasMany { return $this->hasMany(Deal::class); }
}
