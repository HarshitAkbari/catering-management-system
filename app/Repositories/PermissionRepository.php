<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    protected array $searchable = ['name', 'description'];

    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    /**
     * Find permission by name
     */
    public function findByName(string $name): ?Permission
    {
        return $this->model->where('name', $name)->first();
    }
}

