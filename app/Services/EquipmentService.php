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
    public function getByTenant(int $tenantId): Collection
    {
        return $this->repository->getByTenant($tenantId);
    }

    /**
     * Get available equipment
     */
    public function getAvailable(int $tenantId): Collection
    {
        return $this->repository->getAvailable($tenantId);
    }
}

