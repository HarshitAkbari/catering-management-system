<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'role',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * Get the tenant that owns the staff.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the orders (events) for the staff.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'event_staff')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the attendance records for the staff.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}

