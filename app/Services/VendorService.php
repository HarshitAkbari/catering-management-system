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
    public function getByTenant(int $tenantId): Collection
    {
        return $this->repository->getByTenant($tenantId);
    }
}

