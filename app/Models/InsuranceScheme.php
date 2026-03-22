<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceScheme extends Model
{
    protected $fillable = ['name', 'copayment_percentage', 'is_active'];

    protected $casts = [
        'is_active'            => 'boolean',
        'copayment_percentage' => 'decimal:2',
    ];

    public function sales(): HasMany { return $this->hasMany(Sale::class); }
}