<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentStock extends Model
{
    protected $table    = 'department_stock';
    protected $fillable = ['batch_id', 'department_id', 'quantity_remaining'];

    public function batch(): BelongsTo      { return $this->belongsTo(Batch::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }

    public function isLowStock(int $t = 20): bool { return $this->quantity_remaining < $t; }
}
