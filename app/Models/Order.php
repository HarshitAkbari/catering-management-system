<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'order_number',
        'event_date',
        'event_time',
        'address',
        'order_type',
        'guest_count',
        'menu_package_id',
        'estimated_cost',
        'status',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'estimated_cost' => 'decimal:2',
        ];
    }

    /**
     * Get the tenant that owns the order.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the package for the order.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'menu_package_id');
    }

    /**
     * Get the invoice for the order.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
