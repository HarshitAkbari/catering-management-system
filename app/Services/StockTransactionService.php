<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\StockTransactionRepository;
use Illuminate\Database\Eloquent\Collection;

class StockTransactionService extends BaseService
{
    protected StockTransactionRepository $repository;

    public function __construct(StockTransactionRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get stock transactions by tenant
     */
    public function getByTenant(int $tenantId, array $relations = []): Collection
    {
        return $this->repository->getByTenant($tenantId, $relations);
    }

    /**
     * Get stock transactions by inventory item
     */
    public function getByInventoryItem(int $inventoryItemId, int $tenantId): Collection
    {
        return $this->repository->getByInventoryItem($inventoryItemId, $tenantId);
    }

    /**
     * Get stock transactions by type
     */
    public function getByType(string $type, int $tenantId): Collection
    {
        return $this->repository->getByType($type, $tenantId);
    }
}

