<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'name',
        'mobile',
        'secondary_mobile',
        'email',
        'address',
    ];

    /**
     * Get the tenant that owns the customer.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
