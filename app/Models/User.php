<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'department_id', 'is_active', 'drawer_number',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    public function department(): BelongsTo  { return $this->belongsTo(Department::class); }
    public function activityLogs(): HasMany  { return $this->hasMany(ActivityLog::class); }
    public function sales(): HasMany         { return $this->hasMany(Sale::class, 'sold_by'); }
    public function invoices(): HasMany      { return $this->hasMany(Invoice::class, 'created_by'); }

    public function isAdmin(): bool          { return $this->role === 'admin'; }
    public function isHodPharmacy(): bool    { return $this->role === 'hod_pharmacy'; }
    public function isCashier(): bool        { return $this->role === 'cashier'; }
    public function isDepartmentUser(): bool { return in_array($this->role, ['pharmacist', 'lab', 'theatre', 'ward']); }
    public function isPharmacyStaff(): bool  { return in_array($this->role, ['pharmacist', 'hod_pharmacy']); }

    public function getRoleLabelAttribute(): string {
        return match($this->role) {
            'hod_pharmacy' => 'HOD Pharmacy',
            'cashier'      => 'Cashier',
            default        => ucfirst($this->role)
        };
    }
}