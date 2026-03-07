<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = ['name', 'generic_name', 'dosage', 'unit', 'category', 'description', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function batches(): HasMany       { return $this->hasMany(Batch::class); }
    public function activeBatches(): HasMany {
        return $this->hasMany(Batch::class)
            ->where('expiry_date', '>', now())
            ->where('quantity_remaining', '>', 0);
    }
}
