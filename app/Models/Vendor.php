<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * Get the tenant that owns the vendor.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the stock transactions for the vendor.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }
}

