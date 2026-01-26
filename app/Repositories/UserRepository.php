<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected array $searchable = ['name', 'email'];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get users by tenant ID
     */
    public function getByTenant(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}

