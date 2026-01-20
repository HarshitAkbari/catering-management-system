<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'event_time_id',
        'event_menu',
        'address',
        'order_type_id',
        'guest_count',
        'estimated_cost',
        'order_status_id',
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
     * Get the invoice for the order.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the equipment assigned to the order.
     */
    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'event_equipment')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the order status.
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * Get the event time.
     */
    public function eventTime(): BelongsTo
    {
        return $this->belongsTo(EventTime::class);
    }

    /**
     * Get the order type.
     */
    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }
}
