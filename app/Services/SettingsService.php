<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EventTime;
use App\Models\InventoryItem;
use App\Models\InventoryUnit;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    // Order Statuses Methods
    public function getOrderStatuses(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = OrderStatus::where('tenant_id', $tenantId);
        
        // Apply name filter
        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }
        
        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && !empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query->paginate($perPage)->appends($filters);
    }

    public function createOrderStatus(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            OrderStatus::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create order status: ' . $e->getMessage(),
            ];
        }
    }

    public function updateOrderStatus(OrderStatus $orderStatus, array $data): array
    {
        try {
            $orderStatus->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteOrderStatus(OrderStatus $orderStatus): array
    {
        try {
            // Check if status is used in orders
            $orderCount = Order::where('order_status_id', $orderStatus->id)->count();

            if ($orderCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete order status. It is being used by {$orderCount} order(s).",
                ];
            }

            $orderStatus->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete order status: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleOrderStatus(OrderStatus $orderStatus): array
    {
        try {
            $orderStatus->update(['is_active' => !$orderStatus->is_active]);

            return [
                'status' => true,
                'is_active' => $orderStatus->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle order status: ' . $e->getMessage(),
            ];
        }
    }

    // Event Times Methods
    public function getEventTimes(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = EventTime::where('tenant_id', $tenantId);
        
        // Apply name filter
        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }
        
        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && !empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query->paginate($perPage)->appends($filters);
    }

    public function createEventTime(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            EventTime::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create event time: ' . $e->getMessage(),
            ];
        }
    }

    public function updateEventTime(EventTime $eventTime, array $data): array
    {
        try {
            $eventTime->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update event time: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteEventTime(EventTime $eventTime): array
    {
        try {
            // Check if event time is used in orders
            $orderCount = Order::where('event_time_id', $eventTime->id)->count();

            if ($orderCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete event time. It is being used by {$orderCount} order(s).",
                ];
            }

            $eventTime->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete event time: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleEventTime(EventTime $eventTime): array
    {
        try {
            $eventTime->update(['is_active' => !$eventTime->is_active]);

            return [
                'status' => true,
                'is_active' => $eventTime->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle event time: ' . $e->getMessage(),
            ];
        }
    }

    // Order Types Methods
    public function getOrderTypes(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = OrderType::where('tenant_id', $tenantId);
        
        // Apply name filter
        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }
        
        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && !empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query->paginate($perPage)->appends($filters);
    }

    public function createOrderType(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            OrderType::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create order type: ' . $e->getMessage(),
            ];
        }
    }

    public function updateOrderType(OrderType $orderType, array $data): array
    {
        try {
            $orderType->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update order type: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteOrderType(OrderType $orderType): array
    {
        try {
            // Check if order type is used in orders
            $orderCount = Order::where('order_type_id', $orderType->id)->count();

            if ($orderCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete order type. It is being used by {$orderCount} order(s).",
                ];
            }

            $orderType->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete order type: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleOrderType(OrderType $orderType): array
    {
        try {
            $orderType->update(['is_active' => !$orderType->is_active]);

            return [
                'status' => true,
                'is_active' => $orderType->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle order type: ' . $e->getMessage(),
            ];
        }
    }

    // Inventory Units Methods
    public function getInventoryUnits(int $tenantId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = InventoryUnit::where('tenant_id', $tenantId);
        
        // Apply name filter
        if (isset($filters['name_like']) && !empty($filters['name_like'])) {
            $query->where('name', 'like', '%' . $filters['name_like'] . '%');
        }
        
        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Apply sorting
        if (isset($filters['sort_by']) && !empty($filters['sort_by'])) {
            $sortOrder = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $sortOrder);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        return $query->paginate($perPage)->appends($filters);
    }

    public function createInventoryUnit(array $data, int $tenantId): array
    {
        try {
            $data['tenant_id'] = $tenantId;
            $data['is_active'] = $data['is_active'] ?? true;

            InventoryUnit::create($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create inventory unit: ' . $e->getMessage(),
            ];
        }
    }

    public function updateInventoryUnit(InventoryUnit $inventoryUnit, array $data): array
    {
        try {
            $inventoryUnit->update($data);

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update inventory unit: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteInventoryUnit(InventoryUnit $inventoryUnit): array
    {
        try {
            // Check if unit is used in inventory items
            $itemCount = InventoryItem::where('inventory_unit_id', $inventoryUnit->id)->count();

            if ($itemCount > 0) {
                return [
                    'status' => false,
                    'message' => "Cannot delete inventory unit. It is being used by {$itemCount} inventory item(s).",
                ];
            }

            $inventoryUnit->delete();

            return ['status' => true];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete inventory unit: ' . $e->getMessage(),
            ];
        }
    }

    public function toggleInventoryUnit(InventoryUnit $inventoryUnit): array
    {
        try {
            $inventoryUnit->update(['is_active' => !$inventoryUnit->is_active]);

            return [
                'status' => true,
                'is_active' => $inventoryUnit->is_active,
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to toggle inventory unit: ' . $e->getMessage(),
            ];
        }
    }
}

