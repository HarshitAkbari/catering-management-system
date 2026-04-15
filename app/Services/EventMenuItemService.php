<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EventMenuItem;
use App\Repositories\EventMenuItemRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EventMenuItemService extends BaseService
{
    protected EventMenuItemRepository $repository;

    public function __construct(EventMenuItemRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get event menu items by tenant
     */
    public function getByTenant(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        // Merge tenant_id filter if not already present
        if (!isset($filters['tenant_id'])) {
            $filters['tenant_id'] = $tenantId;
        }

        return $this->repository->filterAndPaginate(
            $filters,
            ['creator'],
            [],
            $perPage
        );
    }

    /**
     * Get all active event menu items for a tenant (for dropdowns)
     */
    public function getActiveByTenant(int $tenantId): Collection
    {
        return $this->repository->getActiveByTenant($tenantId);
    }

    /**
     * Create event menu item
     */
    public function createItem(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;
            $item = $this->repository->create($data);

            return [
                'status' => true,
                'message' => 'Event menu item created successfully.',
                'item' => $item,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create event menu item: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update event menu item
     */
    public function updateItem(EventMenuItem $item, array $data, int $tenantId): array
    {
        if ($item->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            $this->repository->update($item, $data);

            return [
                'status' => true,
                'message' => 'Event menu item updated successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update event menu item: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Toggle event menu item status
     */
    public function toggleStatus(EventMenuItem $item, int $tenantId): array
    {
        if ($item->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            $this->repository->toggleActivation($item);
            $item->refresh();

            return [
                'status' => true,
                'is_active' => $item->is_active,
                'message' => 'Event menu item status updated successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle event menu item status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete event menu item
     */
    public function deleteItem(EventMenuItem $item, int $tenantId): array
    {
        if ($item->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            // Check if item is used in orders
            $orderCount = $item->orders()->count();

            if ($orderCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete event menu item. It is being used by {$orderCount} order(s).",
                ];
            }

            $this->repository->delete($item);

            return [
                'status' => true,
                'message' => 'Event menu item deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete event menu item: ' . $e->getMessage(),
            ];
        }
    }
}


