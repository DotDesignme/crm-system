<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ServicePricing extends Model {
    protected $table = 'product_pricing';
    protected $fillable = ['product_id','tier_name','price','min_quantity'];
    protected $casts = ['price'=>'decimal:2'];
    public function service(): BelongsTo { return $this->belongsTo(Service::class, 'product_id'); }
}
