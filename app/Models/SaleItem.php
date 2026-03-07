<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'batch_id', 'quantity', 'selling_price', 'purchase_price', 'profit'];
    protected $casts    = [
        'selling_price'  => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'profit'         => 'decimal:2',
    ];

    public function sale(): BelongsTo  { return $this->belongsTo(Sale::class); }
    public function batch(): BelongsTo { return $this->belongsTo(Batch::class); }

    public function getSubtotalAttribute(): float { return $this->selling_price * $this->quantity; }
}
