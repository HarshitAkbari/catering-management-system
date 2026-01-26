<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    protected array $searchable = ['name', 'mobile', 'email'];

    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    /**
     * Find customer by mobile number and tenant
     */
    public function findByMobile(string $mobile, int $tenantId): ?Customer
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('mobile', $mobile)
            ->first();
    }
}

