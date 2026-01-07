<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Support\Collection;

class SearchService
{
    public function search(string $query, int $tenantId, int $limit = 10): array
    {
        $query = trim($query);
        
        if (strlen($query) < 2) {
            return [
                'orders' => collect(),
                'customers' => collect(),
                'inventory' => collect(),
                'vendors' => collect(),
            ];
        }

        return [
            'orders' => $this->searchOrders($query, $tenantId, $limit),
            'customers' => $this->searchCustomers($query, $tenantId, $limit),
            'inventory' => $this->searchInventory($query, $tenantId, $limit),
            'vendors' => $this->searchVendors($query, $tenantId, $limit),
        ];
    }

    private function searchOrders(string $query, int $tenantId, int $limit): Collection
    {
        return Order::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('order_number', 'like', "%{$query}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($query) {
                        $customerQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('mobile', 'like', "%{$query}%");
                    });
            })
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'title' => $order->order_number,
                    'subtitle' => $order->customer->name ?? 'Unknown',
                    'url' => route('orders.show', $order),
                    'type' => 'Order',
                ];
            });
    }

    private function searchCustomers(string $query, int $tenantId, int $limit): Collection
    {
        return Customer::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('mobile', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'title' => $customer->name,
                    'subtitle' => $customer->mobile,
                    'url' => route('customers.show', $customer),
                    'type' => 'Customer',
                ];
            });
    }

    private function searchInventory(string $query, int $tenantId, int $limit): Collection
    {
        return InventoryItem::where('tenant_id', $tenantId)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'subtitle' => 'Stock: ' . $item->current_stock . ' ' . ($item->unit ?? ''),
                    'url' => route('inventory.show', $item),
                    'type' => 'Inventory',
                ];
            });
    }

    private function searchVendors(string $query, int $tenantId, int $limit): Collection
    {
        return Vendor::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('contact_person', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'title' => $vendor->name,
                    'subtitle' => $vendor->contact_person ?? $vendor->phone ?? '',
                    'url' => route('vendors.show', $vendor),
                    'type' => 'Vendor',
                ];
            });
    }
}

