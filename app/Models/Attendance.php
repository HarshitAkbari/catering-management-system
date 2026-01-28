<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'date',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the tenant that owns the attendance.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the staff for the attendance.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
