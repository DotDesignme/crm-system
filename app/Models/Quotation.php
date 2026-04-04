<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class Quotation extends Model {
    protected $fillable = ['quotation_number','deal_id','customer_id','contact_id','created_by','company_id','status','subtotal','tax_rate','tax_amount','discount_rate','discount_amount','total','currency','notes','terms','valid_until'];
    protected $casts = ['subtotal'=>'decimal:2','tax_rate'=>'decimal:2','tax_amount'=>'decimal:2','discount_rate'=>'decimal:2','discount_amount'=>'decimal:2','total'=>'decimal:2','valid_until'=>'date'];
    public function deal(): BelongsTo { return $this->belongsTo(Deal::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function contact(): BelongsTo { return $this->belongsTo(CompanyContact::class,'contact_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Employee::class,'created_by'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
    public function getStatusArAttribute(): string { return match($this->status){'draft'=>'مسودة','sent'=>'مرسلة','accepted'=>'مقبولة','rejected'=>'مرفوضة','expired'=>'منتهية',default=>$this->status}; }
}
