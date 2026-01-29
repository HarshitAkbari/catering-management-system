<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryUnit;
use App\Repositories\InventoryItemRepository;
use App\Repositories\StockTransactionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryService extends BaseService
{
    protected InventoryItemRepository $repository;
    protected StockTransactionRepository $stockTransactionRepository;

    public function __construct(
        InventoryItemRepository $repository,
        StockTransactionRepository $stockTransactionRepository
    ) {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->stockTransactionRepository = $stockTransactionRepository;
    }

    /**
     * Get inventory items by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Merge tenant_id filter if not already present
        if (!isset($filters['tenant_id'])) {
            $filters['tenant_id'] = $tenantId;
        }
        
        // Handle low stock filter
        if (isset($filters['stock_status']) && $filters['stock_status'] === 'low') {
            unset($filters['stock_status']);
            // Use a custom query for low stock
            $query = $this->repository->filter($filters, ['inventoryUnit'], [], true);
            $query->whereRaw('current_stock <= minimum_stock');
            return $this->repository->applyPagination($query, $filters, $perPage);
        }
        
        return $this->repository->filterAndPaginate(
            $filters,
            ['inventoryUnit'],
            [],
            $perPage
        );
    }
    
    /**
     * Get all inventory items (for dropdowns, etc.)
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Get low stock items
     */
    public function getLowStock(int $tenantId): Collection
    {
        return $this->repository->getLowStock($tenantId);
    }

    /**
     * Create inventory item
     */
    public function createItem(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $item = $this->repository->create($data);

            return [
                'status' => true,
                'message' => 'Inventory item created successfully.',
                'item' => $item,
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to create inventory item: ' . $e->getMessage()];
        }
    }

    /**
     * Update inventory item
     */
    public function updateItem(InventoryItem $item, array $data, int $tenantId): array
    {
        if ($item->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            $this->repository->update($item, $data);

            return [
                'status' => true,
                'message' => 'Inventory item updated successfully.',
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update inventory item: ' . $e->getMessage()];
        }
    }

    /**
     * Process stock in transaction
     */
    public function stockIn(array $data, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId) {
                $item = $this->repository->find((int) $data['inventory_item_id']);

                if (!$item || $item->tenant_id !== $tenantId) {
                    return ['status' => false, 'message' => 'Inventory item not found'];
                }

                // Create stock transaction
                $this->stockTransactionRepository->create([
                    'tenant_id' => $tenantId,
                    'inventory_item_id' => $data['inventory_item_id'],
                    'type' => 'in',
                    'quantity' => $data['quantity'],
                    'price' => $data['price'] ?? null,
                    'vendor_id' => $data['vendor_id'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Update stock
                $item->increment('current_stock', $data['quantity']);

                return [
                    'status' => true,
                    'message' => 'Stock added successfully.',
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to add stock: ' . $e->getMessage()];
        }
    }

    /**
     * Process stock out transaction
     */
    public function stockOut(array $data, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId) {
                $item = $this->repository->find((int) $data['inventory_item_id']);

                if (!$item || $item->tenant_id !== $tenantId) {
                    return ['status' => false, 'message' => 'Inventory item not found'];
                }

                if ($item->current_stock < $data['quantity']) {
                    return ['status' => false, 'message' => 'Insufficient stock available.'];
                }

                // Create stock transaction
                $this->stockTransactionRepository->create([
                    'tenant_id' => $tenantId,
                    'inventory_item_id' => $data['inventory_item_id'],
                    'type' => 'out',
                    'quantity' => $data['quantity'],
                    'notes' => $data['notes'] ?? null,
                ]);

                // Update stock
                $item->decrement('current_stock', $data['quantity']);

                return [
                    'status' => true,
                    'message' => 'Stock reduced successfully.',
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to reduce stock: ' . $e->getMessage()];
        }
    }

    /**
     * Get inventory units for a tenant
     */
    public function getInventoryUnits(int $tenantId): Collection
    {
        return InventoryUnit::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })->where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }
}

