<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoleService extends BaseService
{
    protected RoleRepository $repository;
    protected PermissionRepository $permissionRepository;

    public function __construct(RoleRepository $repository, PermissionRepository $permissionRepository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get roles by tenant
     */
    public function getByTenant(int $tenantId): Collection
    {
        return $this->repository->filter(['tenant_id' => $tenantId], ['permissions'])->get();
    }

    /**
     * Get permissions by tenant
     */
    public function getPermissionsByTenant(int $tenantId): Collection
    {
        return $this->permissionRepository->filter(['tenant_id' => $tenantId])->get();
    }

    /**
     * Create role
     */
    public function createRole(array $data, int $tenantId): array
    {
        try {
            return DB::transaction(function () use ($data, $tenantId) {
                $role = $this->repository->create([
                    'tenant_id' => $tenantId,
                    'name' => $data['name'],
                    'display_name' => $data['display_name'] ?? $data['name'],
                    'description' => $data['description'] ?? null,
                ]);

                if (isset($data['permissions'])) {
                    $role->permissions()->sync($data['permissions']);
                }

                return [
                    'status' => true,
                    'message' => 'Role created successfully.',
                    'role' => $role,
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to create role: ' . $e->getMessage()];
        }
    }

    /**
     * Update role
     */
    public function updateRole(Role $role, array $data, int $tenantId): array
    {
        if ($role->tenant_id !== $tenantId) {
            return ['status' => false, 'message' => 'Unauthorized'];
        }

        try {
            return DB::transaction(function () use ($role, $data) {
                $this->repository->update($role, [
                    'display_name' => $data['display_name'] ?? $role->display_name,
                    'description' => $data['description'] ?? $role->description,
                ]);

                if (isset($data['permissions'])) {
                    $role->permissions()->sync($data['permissions']);
                }

                return [
                    'status' => true,
                    'message' => 'Role updated successfully.',
                ];
            });
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to update role: ' . $e->getMessage()];
        }
    }

    /**
     * Assign roles to user
     */
    public function assignRolesToUser(\App\Models\User $user, array $roleIds, int $tenantId): array
    {
        try {
            // Validate roles belong to tenant
            $validRoleIds = $this->repository->filter([
                'tenant_id' => $tenantId,
            ], [], [], true)->whereIn('id', $roleIds)->pluck('id')->toArray();

            $user->roles()->sync($validRoleIds);

            return [
                'status' => true,
                'message' => 'Roles assigned successfully.',
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Failed to assign roles: ' . $e->getMessage()];
        }
    }
}

