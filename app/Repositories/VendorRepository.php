<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Vendor;

class VendorRepository extends BaseRepository
{
    protected array $searchable = ['name', 'contact_person', 'phone', 'email'];

    public function __construct(Vendor $model)
    {
        parent::__construct($model);
    }

    /**
     * Get vendors by tenant ID
     */
    public function getByTenant(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();
    }
}

