<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockTransaction;
use Illuminate\Database\Eloquent\Collection;

class StockTransactionRepository extends BaseRepository
{
    protected array $searchable = ['notes'];

    public function __construct(StockTransaction $model)
    {
        parent::__construct($model);
    }

    /**
     * Get stock transactions by tenant ID
     */
    public function getByTenant(int $tenantId, array $relations = []): Collection
    {
        $query = $this->model->where('tenant_id', $tenantId);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get stock transactions by inventory item
     */
    public function getByInventoryItem(int $inventoryItemId, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('inventory_item_id', $inventoryItemId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get stock transactions by type
     */
    public function getByType(string $type, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

