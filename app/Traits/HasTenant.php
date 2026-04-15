<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where(function ($query) {
                    $query->whereNull('tenant_id')
                        ->orWhere('tenant_id', auth()->user()->tenant_id);
                });
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id && !$model->tenant_id && !$model->is_system) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}

