<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model {
    protected $fillable = ['name','slug','description'];
    public function permissions(): BelongsToMany { return $this->belongsToMany(Permission::class); }
    public function employees(): BelongsToMany { return $this->belongsToMany(Employee::class,'employee_role'); }
}
