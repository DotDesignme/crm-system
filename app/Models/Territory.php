<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
class Territory extends Model {
    protected $fillable = ['name','region','company_id'];
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employees(): BelongsToMany { return $this->belongsToMany(Employee::class); }
}
