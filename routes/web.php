<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('forgot-password');
    Route::get('/lock-screen', function () {
        return view('auth.lock-screen');
    })->name('lock-screen');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::middleware(['permission:orders,orders.view'])->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/calendar', [OrderController::class, 'calendar'])->name('orders.calendar');
    });
    Route::middleware(['permission:orders.create'])->group(function () {
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    });
    Route::middleware(['permission:orders,orders.view'])->group(function () {
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
    Route::middleware(['permission:orders.edit'])->group(function () {
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateGroupStatus'])->name('orders.update-status');
    });
    Route::middleware(['permission:orders.delete'])->group(function () {
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
    
    // Customers
    Route::middleware(['permission:customers,customers.view'])->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    });
    Route::middleware(['permission:customers.create'])->group(function () {
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    });
    Route::middleware(['permission:customers,customers.view'])->group(function () {
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });
    Route::middleware(['permission:customers.edit'])->group(function () {
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    });
    Route::middleware(['permission:customers.delete'])->group(function () {
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });
    
    // Payments
    Route::middleware(['permission:payments,payments.view'])->group(function () {
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });
    Route::middleware(['permission:payments.create'])->group(function () {
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    });
    Route::middleware(['permission:payments.edit'])->group(function () {
        Route::post('payments/update-group', [PaymentController::class, 'updateGroupPaymentStatus'])->name('payments.update-group');
    });
    Route::middleware(['permission:payments,payments.view'])->group(function () {
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });
    Route::middleware(['permission:payments.edit'])->group(function () {
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    });
    Route::middleware(['permission:payments.delete'])->group(function () {
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });
    
    // Invoices
    Route::middleware(['permission:invoices,invoices.view'])->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    });
    Route::middleware(['permission:invoices.create'])->group(function () {
        Route::get('invoices/generate/{orderNumber}', [InvoiceController::class, 'generate'])->name('invoices.generate');
    });
    
    // Inventory - Specific routes must come BEFORE resource route
    Route::middleware(['permission:inventory,inventory.view'])->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    });
    Route::middleware(['permission:inventory.create'])->group(function () {
        Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('inventory/stock-in', [InventoryController::class, 'storeStockIn'])->name('inventory.stock-in.store');
        Route::get('inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('inventory/stock-out', [InventoryController::class, 'storeStockOut'])->name('inventory.stock-out.store');
    });
    Route::middleware(['permission:inventory,inventory.view'])->group(function () {
        Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    });
    Route::middleware(['permission:inventory.edit'])->group(function () {
        Route::get('inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    });
    Route::middleware(['permission:inventory.delete'])->group(function () {
        Route::delete('inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });
    
    // Vendors
    Route::middleware(['permission:vendors,vendors.view'])->group(function () {
        Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    });
    Route::middleware(['permission:vendors.create'])->group(function () {
        Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('vendors', [VendorController::class, 'store'])->name('vendors.store');
    });
    Route::middleware(['permission:vendors,vendors.view'])->group(function () {
        Route::get('vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
    });
    Route::middleware(['permission:vendors.edit'])->group(function () {
        Route::get('vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
        Route::put('vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    });
    Route::middleware(['permission:vendors.delete'])->group(function () {
        Route::delete('vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    });
    
    // Equipment - Specific routes must come BEFORE resource route
    Route::middleware(['permission:equipment,equipment.view'])->group(function () {
        Route::get('equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    });
    Route::middleware(['permission:equipment.create'])->group(function () {
        Route::get('equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    });
    Route::middleware(['permission:equipment,equipment.view'])->group(function () {
        Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    });
    Route::middleware(['permission:equipment.create'])->group(function () {
        Route::get('orders/{order}/assign-equipment', [EquipmentController::class, 'assignToEvent'])->name('equipment.assign');
        Route::post('orders/{order}/assign-equipment', [EquipmentController::class, 'storeAssignment'])->name('equipment.assign.store');
    });
    Route::middleware(['permission:equipment.edit'])->group(function () {
        Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    });
    Route::middleware(['permission:equipment.delete'])->group(function () {
        Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    });
    
    // Reports
    Route::middleware(['permission:reports,reports.view'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('orders', [ReportController::class, 'orders'])->name('orders');
        Route::get('payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
    });
    Route::middleware(['permission:reports.export'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });
    
    // Users Management (Admin only)
    Route::middleware(['permission:users,users.view'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware(['permission:users.create'])->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware(['permission:users.edit'])->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware(['permission:users.delete'])->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Roles & Permissions (Admin only)
    Route::middleware(['permission:roles,roles.view'])->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('users/{user}/assign-roles', [RoleController::class, 'assignRole'])->name('roles.assign');
    });
    Route::middleware(['permission:roles.create'])->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware(['permission:roles.edit'])->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::post('users/{user}/assign-roles', [RoleController::class, 'storeRoleAssignment'])->name('roles.assign.store');
    });
    Route::middleware(['permission:roles.delete'])->group(function () {
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Global Search
    Route::get('search', [\App\Http\Controllers\SearchController::class, 'search'])->name('search');
});
