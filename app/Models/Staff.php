<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'staff_role',
        'staff_role_id',
        'address',
        'status',
    ];

    /**
     * Get the tenant that owns the staff.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the staff role for this staff member.
     */
    public function staffRole(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class);
    }

    /**
     * Get the orders (events) for the staff.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'event_staff')
            ->withPivot('role', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the attendance records for the staff.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if staff is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get upcoming events for this staff.
     */
    public function getUpcomingEvents()
    {
        return $this->orders()
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->get();
    }

    /**
     * Get total events count.
     */
    public function getTotalEvents(): int
    {
        return $this->orders()->count();
    }

    /**
     * Get attendance rate (percentage).
     */
    public function getAttendanceRate(): float
    {
        $totalDays = $this->attendances()->count();
        
        if ($totalDays === 0) {
            return 0.0;
        }

        $presentDays = $this->attendances()
            ->where('status', 'present')
            ->count();

        return round(($presentDays / $totalDays) * 100, 2);
    }
}
