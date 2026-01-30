<?php

namespace App\Providers;

use App\Models\InventoryItem;
use App\Services\MenuService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bind 'inventory' route parameter to InventoryItem model
        Route::bind('inventory', function ($value) {
            return InventoryItem::where('tenant_id', auth()->user()->tenant_id)
                ->findOrFail($value);
        });

        // Register custom Blade directive for permission checks
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Share user menus with sidebar view
        View::composer('elements.sidebar', function ($view) {
            if (auth()->check()) {
                $menuService = app(MenuService::class);
                $user = auth()->user();
                $userMenus = $menuService->getUserMenus($user);
                
                // For admin, if no menus are found, return empty collection to trigger fallback
                // For other users, build the menu tree
                if ($user->isAdmin() && $userMenus->isEmpty()) {
                    $view->with('userMenus', collect());
                } else {
                    $menuTree = $menuService->buildMenuTree($userMenus);
                    $view->with('userMenus', $menuTree);
                }
            }
        });
    }
}
