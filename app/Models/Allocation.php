<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = ['batch_id', 'department_id', 'quantity_allocated', 'allocated_by', 'notes'];

    public function batch(): BelongsTo       { return $this->belongsTo(Batch::class); }
    public function department(): BelongsTo  { return $this->belongsTo(Department::class); }
    public function allocatedBy(): BelongsTo { return $this->belongsTo(User::class, 'allocated_by'); }
}
