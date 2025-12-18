<?php

namespace App\Providers;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Route;
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
    }
}
