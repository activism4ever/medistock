<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'department_id', 'created_by', 'cashier_id',
        'insurance_scheme_id', 'sector', 'drawer_number', 'patient_name', 'patient_id',
        'enrolee_name', 'enrolee_id', 'sale_type', 'status', 'total_amount',
        'copayment_amount', 'insurance_amount', 'notes', 'paid_at', 'dispensed_at',
    ];

    protected $casts = [
        'total_amount'     => 'decimal:2',
        'copayment_amount' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'paid_at'          => 'datetime',
        'dispensed_at'     => 'datetime',
    ];

    public function department(): BelongsTo      { return $this->belongsTo(Department::class); }
    public function createdBy(): BelongsTo        { return $this->belongsTo(User::class, 'created_by'); }
    public function cashier(): BelongsTo          { return $this->belongsTo(User::class, 'cashier_id'); }
    public function insuranceScheme(): BelongsTo  { return $this->belongsTo(InsuranceScheme::class); }
    public function items(): HasMany              { return $this->hasMany(InvoiceItem::class); }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isPaid(): bool      { return $this->status === 'paid'; }
    public function isDispensed(): bool { return $this->status === 'dispensed'; }
    public function isInsurance(): bool { return $this->sale_type === 'insurance'; }
    public function isInformal(): bool  { return $this->sector === 'informal'; }
    public function isFormal(): bool    { return $this->sector === 'formal'; }

    public function sectorLabel(): string {
        return match($this->sector) {
            'formal'   => 'JCHMA Formal Sector',
            'informal' => 'JCHMA Informal Sector',
            default    => $this->insuranceScheme?->name ?? '—'
        };
    }

    public function amountDue(): float {
        if ($this->isInsurance() && $this->isInformal()) return 0.0;
        return $this->isInsurance()
            ? (float) $this->copayment_amount
            : (float) $this->total_amount;
    }
}