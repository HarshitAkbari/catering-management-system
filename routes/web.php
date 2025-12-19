<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::resource('orders', OrderController::class);
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Payments
    Route::resource('payments', PaymentController::class);
    Route::post('payments/update-group', [PaymentController::class, 'updateGroupPaymentStatus'])->name('payments.update-group');
    
    // Inventory - Specific routes must come BEFORE resource route
    Route::get('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('inventory/stock-in', [InventoryController::class, 'storeStockIn'])->name('inventory.stock-in.store');
    Route::get('inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::post('inventory/stock-out', [InventoryController::class, 'storeStockOut'])->name('inventory.stock-out.store');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::resource('inventory', InventoryController::class);
    
    // Vendors
    Route::resource('vendors', VendorController::class);
    
    // Equipment - Specific routes must come BEFORE resource route
    Route::get('orders/{order}/assign-equipment', [EquipmentController::class, 'assignToEvent'])->name('equipment.assign');
    Route::post('orders/{order}/assign-equipment', [EquipmentController::class, 'storeAssignment'])->name('equipment.assign.store');
    Route::resource('equipment', EquipmentController::class);
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('orders', [ReportController::class, 'orders'])->name('orders');
        Route::get('payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
        Route::get('company-profile', [SettingController::class, 'companyProfile'])->name('company-profile');
        Route::post('company-profile', [SettingController::class, 'updateCompanyProfile'])->name('company-profile.update');
        Route::get('invoice-branding', [SettingController::class, 'invoiceBranding'])->name('invoice-branding');
        Route::post('invoice-branding', [SettingController::class, 'updateInvoiceBranding'])->name('invoice-branding.update');
        Route::get('event-types', [SettingController::class, 'eventTypes'])->name('event-types');
        Route::post('event-types', [SettingController::class, 'storeEventType'])->name('event-types.store');
        Route::put('event-types/{eventType}', [SettingController::class, 'updateEventType'])->name('event-types.update');
        Route::delete('event-types/{eventType}', [SettingController::class, 'destroyEventType'])->name('event-types.destroy');
        Route::get('notifications', [SettingController::class, 'notifications'])->name('notifications');
        Route::post('notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
    });
    
    // Roles & Permissions
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('users/{user}/assign-roles', [RoleController::class, 'assignRole'])->name('roles.assign');
    Route::post('users/{user}/assign-roles', [RoleController::class, 'storeRoleAssignment'])->name('roles.assign.store');
    
    // Orders Calendar
    Route::get('orders/calendar', [OrderController::class, 'calendar'])->name('orders.calendar');
});
