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
        'drawer_number', 'sale_type', 'insurance_scheme_id', 'sector',
        'enrolee_name', 'enrolee_id', 'copayment_amount', 'insurance_amount',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'total_profit'     => 'decimal:2',
        'copayment_amount' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
    ];

    public function department(): BelongsTo      { return $this->belongsTo(Department::class); }
    public function soldBy(): BelongsTo          { return $this->belongsTo(User::class, 'sold_by'); }
    public function items(): HasMany             { return $this->hasMany(SaleItem::class); }
    public function insuranceScheme(): BelongsTo { return $this->belongsTo(InsuranceScheme::class); }

    public function isInsurance(): bool { return $this->sale_type === 'insurance'; }
    public function isInformal(): bool  { return $this->sector === 'informal'; }
    public function isFormal(): bool    { return $this->sector === 'formal'; }
}