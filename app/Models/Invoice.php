<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'invoice_number',
        'total_amount',
        'tax',
        'discount',
        'final_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the tenant that owns the invoice.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the order that owns the invoice.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all orders with the same order_number as the invoice's order.
     */
    public function relatedOrders()
    {
        if (!$this->order) {
            return collect();
        }

        return Order::where('tenant_id', $this->tenant_id)
            ->where('order_number', $this->order->order_number)
            ->with('customer')
            ->orderBy('event_date')
            ->get();
    }

    /**
     * Calculate the total amount from all related orders.
     */
    public function calculateTotalFromOrders(): float
    {
        $orders = $this->relatedOrders();
        return (float) $orders->sum('estimated_cost');
    }

    /**
     * Get the customer from the invoice's order.
     */
    public function getCustomerAttribute()
    {
        return $this->order?->customer;
    }
}
