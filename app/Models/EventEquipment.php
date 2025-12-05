<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventEquipment extends Model
{
    protected $table = 'event_equipment';

    protected $fillable = [
        'order_id',
        'equipment_id',
        'quantity',
    ];

    /**
     * Get the order for the event equipment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the equipment.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}

