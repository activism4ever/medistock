<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'batch_id', 'quantity', 'selling_price', 'subtotal'];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'subtotal'      => 'decimal:2',
    ];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function batch(): BelongsTo   { return $this->belongsTo(Batch::class); }
}