<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EquipmentRepository;
use Illuminate\Database\Eloquent\Collection;

class EquipmentService extends BaseService
{
    protected EquipmentRepository $repository;

    public function __construct(EquipmentRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get equipment by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        // Merge tenant_id filter if not already present
        if (!isset($filters['tenant_id'])) {
            $filters['tenant_id'] = $tenantId;
        }
        
        return $this->repository->filterAndPaginate(
            $filters,
            [],
            [],
            $perPage
        );
    }
    
    /**
     * Get all equipment (for dropdowns, etc.)
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get available equipment
     */
    public function getAvailable(int $tenantId): Collection
    {
        return $this->repository->getAvailable($tenantId);
    }
}

