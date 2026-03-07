<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number', 'department_id', 'sold_by', 'patient_name',
        'patient_id', 'total_amount', 'total_profit', 'status', 'notes',
    ];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_profit' => 'decimal:2',
    ];

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function soldBy(): BelongsTo     { return $this->belongsTo(User::class, 'sold_by'); }
    public function items(): HasMany        { return $this->hasMany(SaleItem::class); }
}
