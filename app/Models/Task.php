<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
class Task extends Model {
    protected $fillable = ['title','description','type','priority','status','assigned_to','created_by','taskable_type','taskable_id','due_at','completed_at','company_id'];
    protected $casts = ['due_at'=>'datetime','completed_at'=>'datetime'];
    public function assignedTo(): BelongsTo { return $this->belongsTo(Employee::class,'assigned_to'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(Employee::class,'created_by'); }
    public function taskable(): MorphTo { return $this->morphTo(); }
    public function customer(): BelongsTo { return $this->belongsTo(Company::class); }
    public function getTypeArAttribute(): string { return match($this->type){'follow_up'=>'متابعة','call'=>'اتصال','email'=>'إرسال إيميل','meeting'=>'اجتماع','other'=>'أخرى',default=>$this->type}; }
    public function getPriorityArAttribute(): string { return match($this->priority){'low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة',default=>$this->priority}; }
    public function getStatusArAttribute(): string { return match($this->status){'pending'=>'قيد الانتظار','in_progress'=>'قيد التنفيذ','completed'=>'مكتملة','overdue'=>'متأخرة',default=>$this->status}; }
}
