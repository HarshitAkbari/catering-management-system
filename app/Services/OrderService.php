<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService extends BaseService
{
    protected OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get orders grouped by order number for tenant
     */
    public function getGroupedOrders(int $tenantId, array $filters = []): SupportCollection
    {
        // Build base query with tenant filter
        $baseFilters = ['tenant_id' => $tenantId];
        
        // Handle customer search separately as it needs special handling
        $customerSearch = null;
        if (isset($filters['customer']) && isset($filters['customer']['_or_where'])) {
            $customerSearch = $filters['customer']['_or_where'][0]['search_term'] ?? null;
            unset($filters['customer']);
        }
        
        // Extract sorting parameters for group-level sorting
        $sortBy = $filters['sort_by'] ?? null;
        $sortOrder = $filters['sort_order'] ?? 'asc';
        
        // Keep sort parameters in filters for repository (it will handle relationship sorting)
        // But we'll also apply group-level sorting after grouping
        $mergedFilters = array_merge($baseFilters, $filters);
        
        // Get filtered orders (return Query Builder, not Collection)
        $query = $this->repository->filter($mergedFilters, ['customer'], [], true);
        
        // Apply customer search if provided
        if ($customerSearch) {
            $query->whereHas('customer', function ($q) use ($customerSearch) {
                $q->where('name', 'like', "%{$customerSearch}%")
                  ->orWhere('mobile', 'like', "%{$customerSearch}%")
                  ->orWhere('email', 'like', "%{$customerSearch}%");
            });
        }
        
        // Default sorting if no sort_by specified
        if (!$sortBy) {
            $query->orderBy('created_at', 'desc');
        }
        
        $allOrders = $query->get();
        
        // Group orders by order_number
        $grouped = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'order_number' => $orderNumber,
                'customer' => $firstOrder->customer,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'status' => $this->getGroupStatus($orderGroup),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
            ];
        })->values();
        
        // Apply group-level sorting if needed
        if ($sortBy) {
            $grouped = $this->applyGroupSorting($grouped, $sortBy, $sortOrder);
        } else {
            $grouped = $grouped->sortByDesc('created_at')->values();
        }
        
        return $grouped;
    }
    
    /**
     * Apply sorting to grouped orders collection
     */
    private function applyGroupSorting(SupportCollection $grouped, string $sortBy, string $sortOrder): SupportCollection
    {
        // Handle relationship sorting (customer.name, customer.mobile, customer.email)
        if (str_starts_with($sortBy, 'customer.')) {
            $field = str_replace('customer.', '', $sortBy);
            if ($sortOrder === 'asc') {
                return $grouped->sortBy(function ($group) use ($field) {
                    return $group['customer']->{$field} ?? '';
                })->values();
            } else {
                return $grouped->sortByDesc(function ($group) use ($field) {
                    return $group['customer']->{$field} ?? '';
                })->values();
            }
        }
        
        // Handle group-level fields (total_amount, payment_status)
        if (in_array($sortBy, ['total_amount', 'payment_status', 'status', 'created_at'])) {
            if ($sortOrder === 'asc') {
                return $grouped->sortBy($sortBy)->values();
            } else {
                return $grouped->sortByDesc($sortBy)->values();
            }
        }
        
        // Default: sort by created_at desc
        return $grouped->sortByDesc('created_at')->values();
    }

    /**
     * Get orders by order number
     */
    public function getByOrderNumber(string $orderNumber, int $tenantId, array $filters = []): Collection
    {
        $baseFilters = [
            'tenant_id' => $tenantId,
            'order_number' => $orderNumber,
        ];

        // Merge additional filters
        $mergedFilters = array_merge($baseFilters, $filters);

        // Use repository filter method if filters are provided, otherwise use direct method
        if (!empty($filters)) {
            return $this->repository->filter($mergedFilters, [], [], false)
                ->orderBy('event_date', 'asc')
                ->orderBy('event_time', 'asc')
                ->get();
        }

        return $this->repository->getByOrderNumber($orderNumber, $tenantId);
    }

    /**
     * Create orders for events
     */
    public function createOrders(array $eventsData, array $customerData, string $address, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($eventsData, $customerData, $address, $tenantId) {
                // Find or create customer
                $customerService = app(CustomerService::class);
                $customer = $customerService->findOrCreateByMobile(
                    $customerData['customer_mobile'],
                    $tenantId,
                    [
                        'name' => $customerData['customer_name'],
                        'email' => $customerData['customer_email'] ?? null,
                    ]
                );

                // Determine order number
                $firstEventDate = $eventsData[0]['event_date'];
                $existingOrder = $this->repository->filter([
                    'tenant_id' => $tenantId,
                    'customer_id' => $customer->id,
                    'event_date' => $firstEventDate,
                ])->first();

                $orderNumber = $existingOrder?->order_number ?? $this->generateOrderNumber($tenantId);

                // Create orders for each event
                $createdOrders = [];
                foreach ($eventsData as $event) {
                    $order = $this->repository->create([
                        'tenant_id' => $tenantId,
                        'customer_id' => $customer->id,
                        'order_number' => $orderNumber,
                        'address' => $address,
                        'event_date' => $event['event_date'],
                        'event_time' => $event['event_time'],
                        'event_menu' => $event['event_menu'],
                        'order_type' => $event['order_type'] ?? null,
                        'guest_count' => $event['guest_count'],
                        'estimated_cost' => $event['cost'],
                        'status' => 'pending',
                        'payment_status' => 'pending',
                    ]);

                    $createdOrders[] = $order;
                }

                return [
                    'status' => true,
                    'order_number' => $orderNumber,
                    'orders' => $createdOrders,
                    'count' => count($createdOrders),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to create orders: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update order
     */
    public function updateOrder(Order $order, array $data, int $tenantId): array
    {
        if ($order->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            return DB::transaction(function () use ($order, $data, $tenantId) {
                // Update or create customer if mobile changed
                if (isset($data['customer_mobile'])) {
                    $customerService = app(CustomerService::class);
                    $customer = $customerService->findOrCreateByMobile(
                        $data['customer_mobile'],
                        $tenantId,
                        ['name' => $data['customer_name'] ?? $order->customer->name]
                    );
                    $data['customer_id'] = $customer->id;
                    unset($data['customer_mobile'], $data['customer_name']);
                }

                $this->repository->update($order, $data);

                return ['status' => true, 'message' => 'Order updated successfully'];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update order: ' . $e->getMessage()];
        }
    }

    /**
     * Update orders for events (handles multiple orders with same order_number)
     */
    public function updateOrders(string $orderNumber, array $eventsData, array $customerData, string $address, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($orderNumber, $eventsData, $customerData, $address, $tenantId) {
                // Verify that the order number belongs to this tenant
                $existingOrders = $this->repository->getByOrderNumber($orderNumber, $tenantId);
                if ($existingOrders->isEmpty()) {
                    return ['status' => false, 'message' => 'Order not found or unauthorized'];
                }

                // Find or create customer
                $customerService = app(CustomerService::class);
                $customer = $customerService->findOrCreateByMobile(
                    $customerData['customer_mobile'],
                    $tenantId,
                    [
                        'name' => $customerData['customer_name'],
                        'email' => $customerData['customer_email'] ?? null,
                    ]
                );

                // Get existing order IDs to track which ones to keep
                $existingOrderIds = $existingOrders->pluck('id')->toArray();
                $updatedOrderIds = [];

                // Update or create orders for each event
                foreach ($eventsData as $event) {
                    // Check if this event matches an existing order (by date, time, menu)
                    $existingOrder = $existingOrders->first(function ($order) use ($event) {
                        return $order->event_date->format('Y-m-d') === $event['event_date'] &&
                               $order->event_time === $event['event_time'] &&
                               $order->event_menu === $event['event_menu'];
                    });

                    if ($existingOrder) {
                        // Update existing order
                        $this->repository->update($existingOrder, [
                            'customer_id' => $customer->id,
                            'address' => $address,
                            'event_date' => $event['event_date'],
                            'event_time' => $event['event_time'],
                            'event_menu' => $event['event_menu'],
                            'order_type' => $event['order_type'] ?? null,
                            'guest_count' => $event['guest_count'],
                            'estimated_cost' => $event['cost'],
                        ]);
                        $updatedOrderIds[] = $existingOrder->id;
                    } else {
                        // Create new order for this event
                        $newOrder = $this->repository->create([
                            'tenant_id' => $tenantId,
                            'customer_id' => $customer->id,
                            'order_number' => $orderNumber,
                            'address' => $address,
                            'event_date' => $event['event_date'],
                            'event_time' => $event['event_time'],
                            'event_menu' => $event['event_menu'],
                            'order_type' => $event['order_type'] ?? null,
                            'guest_count' => $event['guest_count'],
                            'estimated_cost' => $event['cost'],
                            'status' => 'pending',
                            'payment_status' => 'pending',
                        ]);
                        $updatedOrderIds[] = $newOrder->id;
                    }
                }

                // Delete orders that are no longer in the events array
                $ordersToDelete = array_diff($existingOrderIds, $updatedOrderIds);
                if (!empty($ordersToDelete)) {
                    $this->repository->filter([
                        'id' => $ordersToDelete,
                        'tenant_id' => $tenantId,
                    ], [], [], true)->delete();
                }

                return [
                    'status' => true,
                    'order_number' => $orderNumber,
                    'count' => count($eventsData),
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to update orders: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update payment status for all orders with same order number
     */
    public function updateGroupPaymentStatus(string $orderNumber, string $paymentStatus, int $tenantId): array
    {
        try {
            $updatedCount = $this->repository->filter([
                'tenant_id' => $tenantId,
                'order_number' => $orderNumber,
            ], [], [], true)->update(['payment_status' => $paymentStatus]);

            return [
                'status' => true,
                'count' => $updatedCount,
                'message' => "Payment status updated to '{$paymentStatus}' for {$updatedCount} order(s).",
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update payment status: ' . $e->getMessage()];
        }
    }

    /**
     * Update status for all orders with same order number
     */
    public function updateGroupStatus(string $orderNumber, string $status, int $tenantId): array
    {
        try {
            $updatedCount = $this->repository->filter([
                'tenant_id' => $tenantId,
                'order_number' => $orderNumber,
            ], [], [], true)->update(['status' => $status]);

            return [
                'status' => true,
                'count' => $updatedCount,
                'message' => "Order status updated to '{$status}' for {$updatedCount} order(s).",
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update order status: ' . $e->getMessage()];
        }
    }

    /**
     * Get orders for calendar view
     */
    public function getCalendarOrders(int $tenantId): SupportCollection
    {
        return $this->repository->getByTenant($tenantId, ['customer'])->map(function ($order) {
            return [
                'id' => $order->id,
                'title' => $order->customer->name . ' - ' . $order->order_number,
                'start' => $order->event_date->format('Y-m-d'),
                'url' => route('orders.show', $order),
            ];
        });
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(int $tenantId): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        } while ($this->repository->orderNumberExists($orderNumber, $tenantId));

        return $orderNumber;
    }

    /**
     * Get group status - returns status if all orders have same status, otherwise "mixed"
     */
    private function getGroupStatus($orderGroup): string
    {
        $statuses = $orderGroup->pluck('status')->unique()->filter();
        return $statuses->count() === 1 ? $statuses->first() : 'mixed';
    }

    /**
     * Get group payment status - returns payment status if all orders have same status, otherwise "mixed"
     */
    private function getGroupPaymentStatus($orderGroup): string
    {
        $paymentStatuses = $orderGroup->pluck('payment_status')->unique()->filter();
        return $paymentStatuses->count() === 1 ? $paymentStatuses->first() : 'mixed';
    }
}

