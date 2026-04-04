<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};
class Commission extends Model {
    protected $fillable = ['employee_id','deal_id','deal_value','commission_rate','commission_amount','status','paid_at','company_id'];
    protected $casts = ['deal_value'=>'decimal:2','commission_rate'=>'decimal:2','commission_amount'=>'decimal:2','paid_at'=>'date'];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function deal(): BelongsTo { return $this->belongsTo(Deal::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
