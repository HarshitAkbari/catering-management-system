<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\VendorRepository;
use Illuminate\Database\Eloquent\Collection;

class VendorService extends BaseService
{
    protected VendorRepository $repository;

    public function __construct(VendorRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get vendors by tenant
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
}

