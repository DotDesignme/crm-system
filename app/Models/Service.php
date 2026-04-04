<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
class Service extends Model {
    protected $table = 'products';
    protected $fillable = ['name','sku','description','thickness','material_type','application_method','unit','unit_type','category','cost_price','selling_price','currency','is_service','is_active','company_id'];
    protected $casts = [
        'cost_price'=>'decimal:2',
        'selling_price'=>'decimal:2',
        'is_service'=>'boolean',
        'is_active'=>'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            $service->is_service = true;
        });
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function pricings(): HasMany { return $this->hasMany(ProductPricing::class); }
    public function getMarginAttribute(): float { if($this->selling_price==0)return 0; return round((($this->selling_price-$this->cost_price)/$this->selling_price)*100,2); }
}
