<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'name',
        'equipment_category_id',
        'quantity',
        'available_quantity',
    ];

    /**
     * Get the tenant that owns the equipment.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the equipment category for this equipment.
     */
    public function equipmentCategory(): BelongsTo
    {
        return $this->belongsTo(EquipmentCategory::class);
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

