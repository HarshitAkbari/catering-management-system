<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository extends BaseRepository
{
    protected array $searchable = ['order_number', 'event_menu'];

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get orders by tenant ID
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
     * Get orders by order number
     */
    public function getByOrderNumber(string $orderNumber, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('order_number', $orderNumber)
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->get();
    }

    /**
     * Get orders by customer ID
     */
    public function getByCustomer(int $customerId, int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if order number exists for tenant
     */
    public function orderNumberExists(string $orderNumber, int $tenantId): bool
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('order_number', $orderNumber)
            ->exists();
    }
}

