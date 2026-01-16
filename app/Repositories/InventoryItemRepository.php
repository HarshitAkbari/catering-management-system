<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Collection;

class InventoryItemRepository extends BaseRepository
{
    protected array $searchable = ['name', 'description'];

    public function __construct(InventoryItem $model)
    {
        parent::__construct($model);
    }

    /**
     * Get inventory items by tenant ID
     */
    public function getByTenant(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get low stock items for tenant
     */
    public function getLowStock(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->whereRaw('current_stock <= minimum_stock')
            ->get();
    }
}

