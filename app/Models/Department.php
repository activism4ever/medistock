<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function users(): HasMany    { return $this->hasMany(User::class); }
    public function allocations(): HasMany { return $this->hasMany(Allocation::class); }
    public function stock(): HasMany    { return $this->hasMany(DepartmentStock::class); }
    public function sales(): HasMany    { return $this->hasMany(Sale::class); }
}
