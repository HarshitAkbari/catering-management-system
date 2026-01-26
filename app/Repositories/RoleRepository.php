<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    protected array $searchable = ['name'];

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * Find role by name
     */
    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }
}

