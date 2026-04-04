<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class Invoice extends Model {
    protected $fillable = ['invoice_number','deal_id','quotation_id','customer_id','contact_id','created_by','company_id','status','subtotal','tax_rate','tax_amount','discount_rate','discount_amount','total','paid_amount','currency','issue_date','due_date','notes'];
    protected $casts = ['subtotal'=>'decimal:2','tax_rate'=>'decimal:2','tax_amount'=>'decimal:2','discount_rate'=>'decimal:2','discount_amount'=>'decimal:2','total'=>'decimal:2','paid_amount'=>'decimal:2','issue_date'=>'date','due_date'=>'date'];
    public function deal(): BelongsTo { return $this->belongsTo(Deal::class); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Employee::class,'created_by'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function getBalanceAttribute(): float { return $this->total - $this->paid_amount; }
    public function getPaidPercentageAttribute(): float { if($this->total==0)return 0; return round(($this->paid_amount/$this->total)*100,2); }
    public function getStatusArAttribute(): string { return match($this->status){'draft'=>'مسودة','sent'=>'مرسلة','paid'=>'مدفوعة','partial'=>'مدفوعة جزئياً','overdue'=>'متأخرة','cancelled'=>'ملغاة',default=>$this->status}; }
}
