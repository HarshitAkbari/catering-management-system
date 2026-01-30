<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class MenuService extends BaseService
{
    public function __construct()
    {
        // MenuService doesn't use a repository as it's a utility service
        parent::__construct(new \App\Repositories\PermissionRepository(new \App\Models\Permission()));
    }

    /**
     * Get all menus accessible to a user.
     */
    public function getUserMenus(User $user): Collection
    {
        // Admin sees all menus
        if ($user->isAdmin()) {
            return Menu::where('tenant_id', $user->tenant_id)
                ->active()
                ->ordered()
                ->get();
        }

        // First, try to get menus from user's roles in pivot table
        $menuIds = $user->roles()
            ->with('menus')
            ->get()
            ->pluck('menus')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // If no menus found in pivot table, try to get from Role model based on enum role
        if (empty($menuIds) && ($user->isManager() || $user->isStaff())) {
            $roleModel = Role::where('tenant_id', $user->tenant_id)
                ->where('name', strtolower($user->role))
                ->first();
            
            if ($roleModel) {
                // Sync the role to user if not already synced
                if (!$user->roles()->where('roles.id', $roleModel->id)->exists()) {
                    $user->roles()->syncWithoutDetaching([$roleModel->id]);
                }
                
                // Get menus from the role model
                $menuIds = $roleModel->menus()->pluck('menus.id')->unique()->toArray();
            }
        }

        if (empty($menuIds)) {
            return Menu::where('tenant_id', $user->tenant_id)->whereIn('id', [])->get();
        }

        return Menu::where('tenant_id', $user->tenant_id)
            ->whereIn('id', $menuIds)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Check if user has access to a specific menu by route.
     */
    public function hasMenuAccess(User $user, string $menuRoute): bool
    {
        // Admin has access to all menus
        if ($user->isAdmin()) {
            return true;
        }

        $menu = Menu::where('tenant_id', $user->tenant_id)
            ->where('route', $menuRoute)
            ->first();

        if (!$menu) {
            return false;
        }

        // Check if any of user's roles have access to this menu
        return $user->roles()
            ->whereHas('menus', function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->exists();
    }

    /**
     * Build hierarchical menu tree from flat collection.
     */
    public function buildMenuTree(Collection $menus): SupportCollection
    {
        if ($menus->isEmpty()) {
            return collect();
        }

        $tree = collect();
        $indexed = $menus->keyBy('id');

        foreach ($menus as $menu) {
            if ($menu->parent_id === null) {
                // This is a parent menu
                $menu->children = $this->getChildren($menu->id, $indexed);
                $tree->push($menu);
            }
        }

        return $tree->sortBy('order')->values();
    }

    /**
     * Get children menus for a parent menu.
     */
    private function getChildren(?int $parentId, Collection $indexed): SupportCollection
    {
        $children = collect();

        foreach ($indexed as $menu) {
            if ($menu->parent_id === $parentId) {
                $menu->children = $this->getChildren($menu->id, $indexed);
                $children->push($menu);
            }
        }

        return $children->sortBy('order')->values();
    }

    /**
     * Get menus for a specific tenant.
     */
    public function getMenusByTenant(int $tenantId): Collection
    {
        return Menu::where('tenant_id', $tenantId)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get parent menus only.
     */
    public function getParentMenus(int $tenantId): Collection
    {
        return Menu::where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Get child menus for a parent menu.
     */
    public function getChildMenus(int $tenantId, ?int $parentId): Collection
    {
        return Menu::where('tenant_id', $tenantId)
            ->where('parent_id', $parentId)
            ->active()
            ->ordered()
            ->get();
    }
}

