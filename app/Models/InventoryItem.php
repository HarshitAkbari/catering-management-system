<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'name',
        'inventory_unit_id',
        'current_stock',
        'minimum_stock',
        'price_per_unit',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
        ];
    }

    /**
     * Get the tenant that owns the inventory item.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the inventory unit for this item.
     */
    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    /**
     * Get the stock transactions for the inventory item.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}

