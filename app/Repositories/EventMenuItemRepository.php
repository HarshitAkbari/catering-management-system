<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventMenuItem;

class EventMenuItemRepository extends BaseRepository
{
    protected array $searchable = ['name'];

    public function __construct(EventMenuItem $model)
    {
        parent::__construct($model);
    }

    /**
     * Get event menu items by tenant ID
     */
    public function getByTenant(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get active event menu items by tenant ID
     */
    public function getActiveByTenant(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }
}


