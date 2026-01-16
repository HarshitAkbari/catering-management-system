<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class PermissionService extends BaseService
{
    public function __construct()
    {
        // PermissionService doesn't use a repository as it's a utility service
        // We'll create a dummy repository just to satisfy BaseService constructor
        parent::__construct(new \App\Repositories\PermissionRepository(new \App\Models\Permission()));
    }
    /**
     * Check if user has a specific permission.
     * Supports both module-level and action-level permissions.
     */
    public function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$user->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has permission for a module (module-level).
     */
    public function hasModuleAccess(User $user, string $module): bool
    {
        return $user->hasPermission($module);
    }

    /**
     * Check if user can perform an action on a module (action-level).
     */
    public function canPerformAction(User $user, string $module, string $action): bool
    {
        $permission = "{$module}.{$action}";
        return $user->hasPermission($permission);
    }

    /**
     * Get all permissions for a user.
     */
    public function getUserPermissions(User $user): array
    {
        if ($user->isAdmin()) {
            // Admin has all permissions - return a list of all possible permissions
            return $this->getAllPermissions();
        }

        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[$permission->name] = $permission->name;
            }
        }

        return array_values($permissions);
    }

    /**
     * Get all available permissions in the system.
     */
    public function getAllPermissions(): array
    {
        return [
            // Module-level permissions
            'orders',
            'customers',
            'inventory',
            'invoices',
            'payments',
            'reports',
            'users',
            'roles',
            'vendors',
            'equipment',
            
            // Action-level permissions for orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.export',
            
            // Action-level permissions for customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.export',
            
            // Action-level permissions for inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.export',
            
            // Action-level permissions for invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.export',
            
            // Action-level permissions for payments
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.export',
            
            // Action-level permissions for reports
            'reports.view',
            'reports.export',
            
            // Action-level permissions for users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Action-level permissions for roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Action-level permissions for vendors
            'vendors.view',
            'vendors.create',
            'vendors.edit',
            'vendors.delete',
            
            // Action-level permissions for equipment
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',
        ];
    }
}

