<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'medicine_id', 'batch_number', 'expiry_date', 'purchase_price',
        'margin_percentage', 'selling_price', 'receipt_no', 'invoice_no',
        'quantity_purchased', 'quantity_remaining', 'created_by',
    ];

    protected $casts = [
        'expiry_date'       => 'date',
        'purchase_price'    => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'selling_price'     => 'decimal:2',
    ];

    public function medicine(): BelongsTo         { return $this->belongsTo(Medicine::class); }
    public function creator(): BelongsTo          { return $this->belongsTo(User::class, 'created_by'); }
    public function allocations(): HasMany        { return $this->hasMany(Allocation::class); }
    public function departmentStocks(): HasMany   { return $this->hasMany(DepartmentStock::class); }
    public function saleItems(): HasMany          { return $this->hasMany(SaleItem::class); }

    public function isExpired(): bool            { return $this->expiry_date->isPast(); }
    public function isExpiringSoon(int $d = 30): bool {
        return $this->expiry_date->isBefore(now()->addDays($d)) && !$this->isExpired();
    }

    public static function calculateSellingPrice(float $price, float $margin): float {
        return round($price + ($price * $margin / 100), 2);
    }
}
