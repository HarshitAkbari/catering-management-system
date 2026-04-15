<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->seedRolesAndPermissions($tenant);
        }
    }

    /**
     * Seed roles and permissions for a specific tenant.
     */
    public function seedRolesAndPermissions(Tenant $tenant): void
    {
        // Create permissions
        $permissions = $this->createPermissions($tenant);

        // Create roles
        $adminRole = $this->createRole($tenant, 'admin', 'Admin', 'Full system access');
        $managerRole = $this->createRole($tenant, 'manager', 'Manager', 'Management access without user/role management');
        $staffRole = $this->createRole($tenant, 'staff', 'Staff', 'Limited access - view and create only');

        // Assign permissions to roles
        $this->assignPermissionsToRole($adminRole, $permissions);
        $this->assignPermissionsToRole($managerRole, $this->getManagerPermissions($permissions));
        $this->assignPermissionsToRole($staffRole, $this->getStaffPermissions($permissions));

        // Sync existing users with their role models
        $this->syncUsersWithRoles($tenant);
    }

    /**
     * Create all permissions for a tenant.
     */
    private function createPermissions(Tenant $tenant): array
    {
        $permissions = [];

        // Module-level permissions
        // Note: 'staff' and 'attendance' modules added for Staff Management Module
        $modules = ['orders', 'customers', 'inventory', 'invoices', 'payments', 'reports', 'users', 'roles', 'vendors', 'equipment', 'staff', 'attendance'];
        foreach ($modules as $module) {
            $permissions[$module] = Permission::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $module,
                ],
                [
                    'display_name' => ucfirst($module),
                    'description' => "Full access to {$module} module",
                ]
            );
        }

        // Action-level permissions
        // Creates: staff.view, staff.create, staff.edit, staff.delete, staff.export
        // Creates: attendance.view, attendance.create, attendance.edit, attendance.delete, attendance.export
        $actions = ['view', 'create', 'edit', 'delete', 'export'];
        $modulesWithActions = ['orders', 'customers', 'inventory', 'invoices', 'payments', 'vendors', 'equipment', 'staff', 'attendance'];
        
        foreach ($modulesWithActions as $module) {
            foreach ($actions as $action) {
                $name = "{$module}.{$action}";
                $permissions[$name] = Permission::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name' => $name,
                    ],
                    [
                        'display_name' => ucfirst($module) . ' - ' . ucfirst($action),
                        'description' => "Permission to {$action} {$module}",
                    ]
                );
            }
        }

        // Reports specific permissions
        $permissions['reports.view'] = Permission::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.view',
            ],
            [
                'display_name' => 'Reports - View',
                'description' => 'Permission to view reports',
            ]
        );

        $permissions['reports.export'] = Permission::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.export',
            ],
            [
                'display_name' => 'Reports - Export',
                'description' => 'Permission to export reports',
            ]
        );

        // Users specific permissions
        $userActions = ['view', 'create', 'edit', 'delete'];
        foreach ($userActions as $action) {
            $name = "users.{$action}";
            $permissions[$name] = Permission::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $name,
                ],
                [
                    'display_name' => 'Users - ' . ucfirst($action),
                    'description' => "Permission to {$action} users",
                ]
            );
        }

        // Roles specific permissions
        $roleActions = ['view', 'create', 'edit', 'delete'];
        foreach ($roleActions as $action) {
            $name = "roles.{$action}";
            $permissions[$name] = Permission::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $name,
                ],
                [
                    'display_name' => 'Roles - ' . ucfirst($action),
                    'description' => "Permission to {$action} roles",
                ]
            );
        }

        return $permissions;
    }

    /**
     * Create a role for a tenant.
     */
    private function createRole(Tenant $tenant, string $name, string $displayName, string $description): Role
    {
        return Role::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => $name,
            ],
            [
                'display_name' => $displayName,
                'description' => $description,
            ]
        );
    }

    /**
     * Assign permissions to a role.
     */
    private function assignPermissionsToRole(Role $role, array $permissions): void
    {
        $permissionIds = collect($permissions)->pluck('id')->toArray();
        $role->permissions()->sync($permissionIds);
    }

    /**
     * Get manager permissions (all except users and roles management).
     */
    private function getManagerPermissions(array $allPermissions): array
    {
        $managerPermissions = [];
        
        foreach ($allPermissions as $key => $permission) {
            // Exclude users and roles management
            if (strpos($key, 'users.') === 0 || strpos($key, 'roles.') === 0) {
                continue;
            }
            if ($key === 'users' || $key === 'roles') {
                continue;
            }
            $managerPermissions[$key] = $permission;
        }

        return $managerPermissions;
    }

    /**
     * Get staff permissions (view and create only).
     */
    private function getStaffPermissions(array $allPermissions): array
    {
        $staffPermissions = [];
        
        foreach ($allPermissions as $key => $permission) {
            // Only include view and create permissions
            if (strpos($key, '.view') !== false || strpos($key, '.create') !== false) {
                $staffPermissions[$key] = $permission;
            }
        }

        return $staffPermissions;
    }

    /**
     * Sync existing users with their role models based on enum role.
     */
    private function syncUsersWithRoles(Tenant $tenant): void
    {
        $users = User::where('tenant_id', $tenant->id)->get();

        foreach ($users as $user) {
            $roleModel = Role::where('tenant_id', $tenant->id)
                ->where('name', $user->role)
                ->first();

            if ($roleModel) {
                $user->roles()->syncWithoutDetaching([$roleModel->id]);
            }
        }
    }
}

