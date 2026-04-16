<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStaff extends Model
{
    protected $table = 'event_staff';

    protected $fillable = [
        'order_id',
        'staff_id',
        'role',
        'notes',
    ];

    /**
     * Get the order for the event staff.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the staff.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
