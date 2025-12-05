<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => 'string',
        ];
    }

    /**
     * Get the tenant that owns the attendance record.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the staff member.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}

