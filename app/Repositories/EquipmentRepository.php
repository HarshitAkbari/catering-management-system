<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Collection;

class EquipmentRepository extends BaseRepository
{
    protected array $searchable = ['name', 'description'];

    public function __construct(Equipment $model)
    {
        parent::__construct($model);
    }

    /**
     * Get equipment by tenant ID
     */
    public function getByTenant(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get available equipment (not assigned to events)
     */
    public function getAvailable(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
    }
}

