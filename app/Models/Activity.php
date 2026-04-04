<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
class Activity extends Model {
    protected $fillable = ['employee_id','type','subject','description','activitiable_type','activitiable_id','metadata','company_id'];
    protected $casts = ['metadata'=>'array'];
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function activitiable(): MorphTo { return $this->morphTo(); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function getTypeArAttribute(): string { return match($this->type){'created'=>'إنشاء','updated'=>'تحديث','status_changed'=>'تغيير حالة','note_added'=>'إضافة ملاحظة','call'=>'مكالمة','email'=>'إيميل','meeting'=>'اجتماع','deal_created'=>'إنشاء صفقة','deal_moved'=>'نقل صفقة','template_applied'=>'تطبيق قالب',default=>$this->type}; }
    public function getIconAttribute(): string { 
        return match($this->type) { 
            'deal_created','created' => 'fas fa-plus', 
            'deal_moved','status_changed' => 'fas fa-arrow-right', 
            'note_added' => 'fas fa-sticky-note', 
            'call','whatsapp' => 'fas fa-phone', 
            'email' => 'fas fa-envelope', 
            'meeting' => 'fas fa-users', 
            'template_applied' => 'fas fa-magic',
            default => 'fas fa-circle' 
        }; 
    }
}
