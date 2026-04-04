<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Contract extends Model {
    protected $fillable = ['contract_number','title','customer_id','deal_id','created_by','company_id','status','value','currency','start_date','end_date','auto_renew','renewal_period_months','renewal_date','file_path','terms','notes'];
    protected $casts = ['value'=>'decimal:2','start_date'=>'date','end_date'=>'date','renewal_date'=>'date','auto_renew'=>'boolean'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function deal(): BelongsTo { return $this->belongsTo(Deal::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Employee::class,'created_by'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function getDaysUntilRenewalAttribute(): ?int { if(!$this->renewal_date)return null; return now()->diffInDays($this->renewal_date,false); }
    public function getStatusArAttribute(): string { return match($this->status){'draft'=>'مسودة','active'=>'نشط','expired'=>'منتهي','renewed'=>'مجدد','terminated'=>'ملغي',default=>$this->status}; }
}
