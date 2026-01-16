<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
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
    public function getGroupedOrders(int $tenantId): Collection
    {
        $allOrders = $this->repository->getByTenant($tenantId, ['customer']);
        
        return $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
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
        })->values()->sortByDesc('created_at')->values();
    }

    /**
     * Get orders by order number
     */
    public function getByOrderNumber(string $orderNumber, int $tenantId): Collection
    {
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
     * Get orders for calendar view
     */
    public function getCalendarOrders(int $tenantId): Collection
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

