<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'quantity',
        'available_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * Get the tenant that owns the equipment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the orders (events) for the equipment.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'event_equipment')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}

