<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService extends BaseService
{
    protected CustomerRepository $repository;

    public function __construct(CustomerRepository $repository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
    }

    /**
     * Get customers by tenant with pagination
     */
    public function getByTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->filterAndPaginate(
            ['tenant_id' => $tenantId],
            [],
            ['orders'],
            $perPage
        );
    }

    /**
     * Find or create customer by mobile
     */
    public function findOrCreateByMobile(string $mobile, int $tenantId, array $additionalData = []): Customer
    {
        $customer = $this->repository->findByMobile($mobile, $tenantId);

        if (!$customer) {
            $data = array_merge([
                'tenant_id' => $tenantId,
                'mobile' => $mobile,
            ], $additionalData);
            
            $customer = $this->repository->create($data);
        } else {
            // Update if additional data provided
            if (!empty($additionalData)) {
                $this->repository->update($customer, $additionalData);
            }
        }

        return $customer;
    }

    /**
     * Get customer with orders grouped by order number
     */
    public function getCustomerWithGroupedOrders(int $customerId, int $tenantId): array
    {
        $customer = $this->repository->find($customerId, ['orders.invoice.payments']);
        
        if (!$customer || $customer->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Customer not found'];
        }

        $allOrders = $customer->orders;
        
        $groupedOrders = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'order_number' => $orderNumber,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'status' => $this->getGroupStatus($orderGroup),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
                'event_date' => $orderGroup->min('event_date'),
            ];
        })->values()->sortByDesc('created_at')->values();

        return [
            'status' => true,
            'customer' => $customer,
            'groupedOrders' => $groupedOrders,
        ];
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

