<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CustomerContact extends Model {
    protected $fillable = ['customer_id','name','position','email','phone','whatsapp','role','is_decision_maker','notes'];
    protected $casts = ['is_decision_maker'=>'boolean'];
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function getRoleArAttribute(): string { return match($this->role){'owner'=>'مالك','manager'=>'مدير','buyer'=>'مشتري','accountant'=>'محاسب','contact'=>'جهة اتصال',default=>$this->role}; }
    public function getRoleNameAttribute(): string { return __('messages.role_' . ($this->role ?? 'contact')); }
}
