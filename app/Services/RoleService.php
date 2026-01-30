<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use App\Models\Permission;
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
        return $this->repository->filter(['tenant_id' => $tenantId], ['permissions', 'menus'])->get();
    }

    /**
     * Get Manager and Staff roles only
     */
    public function getManagerAndStaffRoles(int $tenantId): Collection
    {
        return $this->repository->filter(['tenant_id' => $tenantId], ['permissions', 'menus'], [], true)
            ->whereIn('name', ['manager', 'staff'])
            ->get();
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
                    'permission_type' => $data['permission_type'] ?? null,
                    'write_permissions' => $data['write_permissions'] ?? null,
                ]);

                // Sync menus if provided
                if (isset($data['menu_ids'])) {
                    $role->menus()->sync($data['menu_ids']);
                }

                // Generate and sync permissions based on role configuration
                $this->generatePermissionsFromRole($role);

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
                    'permission_type' => $data['permission_type'] ?? $role->permission_type,
                    'write_permissions' => $data['write_permissions'] ?? $role->write_permissions,
                ]);

                // Sync menus if provided
                if (isset($data['menu_ids'])) {
                    $role->menus()->sync($data['menu_ids']);
                }

                // Generate and sync permissions based on role configuration
                $this->generatePermissionsFromRole($role);

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

    /**
     * Generate permissions from role configuration.
     * Based on selected menus and permission_type, generates appropriate permissions.
     */
    public function generatePermissionsFromRole(Role $role): void
    {
        if (!$role->permission_type) {
            return;
        }

        $menus = $role->menus()->get();
        $permissionNames = [];

        foreach ($menus as $menu) {
            // Extract module name from menu name (e.g., 'orders.list' -> 'orders')
            $menuNameParts = explode('.', $menu->name);
            $module = $menuNameParts[0];

            if ($role->permission_type === 'read') {
                // For read permission, only generate view permission
                $permissionNames[] = "{$module}.view";
            } elseif ($role->permission_type === 'write') {
                // For write permission, generate view + selected write permissions
                $permissionNames[] = "{$module}.view";

                $writePermissions = $role->write_permissions ?? [];
                foreach ($writePermissions as $action) {
                    $permissionNames[] = "{$module}.{$action}";
                }
            }
        }

        // Remove duplicates
        $permissionNames = array_unique($permissionNames);

        // Get or create permissions and sync to role
        $permissionIds = [];
        foreach ($permissionNames as $permissionName) {
            $permission = Permission::firstOrCreate(
                [
                    'tenant_id' => $role->tenant_id,
                    'name' => $permissionName,
                ],
                [
                    'display_name' => ucfirst(str_replace('.', ' - ', $permissionName)),
                    'description' => "Permission to {$permissionName}",
                ]
            );
            $permissionIds[] = $permission->id;
        }

        $role->permissions()->sync($permissionIds);
    }
}

