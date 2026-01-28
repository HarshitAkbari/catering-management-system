<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AttendanceController;
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
    
    // Password Reset Routes
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    
    Route::get('/lock-screen', function () {
        return view('auth.lock-screen');
    })->name('lock-screen');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', 'tenant'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
    Route::post('change-password', [ProfileController::class, 'updatePassword'])->name('change-password.update');
    
    // Orders Menu
    // Orders - List & Calendar
    Route::middleware(['permission:orders,orders.view'])->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/calendar', [OrderController::class, 'calendar'])->name('orders.calendar');
    });
    // Orders - Create
    Route::middleware(['permission:orders.create'])->group(function () {
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    });
    // Orders - View
    Route::middleware(['permission:orders,orders.view'])->group(function () {
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
    // Orders - Edit
    Route::middleware(['permission:orders.edit'])->group(function () {
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateGroupStatus'])->name('orders.update-status');
    });
    // Orders - Delete
    Route::middleware(['permission:orders.delete'])->group(function () {
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
    
    // Customers Menu
    // Customers - List
    Route::middleware(['permission:customers,customers.view'])->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    });
    // Customers - Create
    Route::middleware(['permission:customers.create'])->group(function () {
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    });
    // Customers - View
    Route::middleware(['permission:customers,customers.view'])->group(function () {
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    });
    // Customers - Edit
    Route::middleware(['permission:customers.edit'])->group(function () {
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    });
    // Customers - Delete
    Route::middleware(['permission:customers.delete'])->group(function () {
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });
    
    // Payments Menu
    // Payments - List
    Route::middleware(['permission:payments,payments.view'])->group(function () {
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });
    // Payments - Create
    Route::middleware(['permission:payments.create'])->group(function () {
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    });
    // Payments - Edit (Group Update)
    Route::middleware(['permission:payments.edit'])->group(function () {
        Route::post('payments/update-group', [PaymentController::class, 'updateGroupPaymentStatus'])->name('payments.update-group');
    });
    // Payments - View
    Route::middleware(['permission:payments,payments.view'])->group(function () {
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });
    // Payments - Edit
    Route::middleware(['permission:payments.edit'])->group(function () {
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    });
    // Payments - Delete
    Route::middleware(['permission:payments.delete'])->group(function () {
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });
    
    // Invoices Menu
    // Invoices - List, View & Download
    Route::middleware(['permission:invoices,invoices.view'])->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    });
    // Invoices - Generate
    Route::middleware(['permission:invoices.create'])->group(function () {
        Route::get('invoices/generate/{orderNumber}', [InvoiceController::class, 'generate'])->name('invoices.generate');
    });
    
    // Inventory Menu
    // Inventory - List & Low Stock
    Route::middleware(['permission:inventory,inventory.view'])->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    });
    // Inventory - Create, Stock In & Stock Out
    Route::middleware(['permission:inventory.create'])->group(function () {
        Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('inventory/stock-in', [InventoryController::class, 'storeStockIn'])->name('inventory.stock-in.store');
        Route::get('inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
        Route::post('inventory/stock-out', [InventoryController::class, 'storeStockOut'])->name('inventory.stock-out.store');
    });
    // Inventory - View
    Route::middleware(['permission:inventory,inventory.view'])->group(function () {
        Route::get('inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    });
    // Inventory - Edit
    Route::middleware(['permission:inventory.edit'])->group(function () {
        Route::get('inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
    });
    // Inventory - Delete
    Route::middleware(['permission:inventory.delete'])->group(function () {
        Route::delete('inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });
    
    // Vendors Menu
    // Vendors - List
    Route::middleware(['permission:vendors,vendors.view'])->group(function () {
        Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
    });
    // Vendors - Create
    Route::middleware(['permission:vendors.create'])->group(function () {
        Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('vendors', [VendorController::class, 'store'])->name('vendors.store');
    });
    // Vendors - View
    Route::middleware(['permission:vendors,vendors.view'])->group(function () {
        Route::get('vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
    });
    // Vendors - Edit
    Route::middleware(['permission:vendors.edit'])->group(function () {
        Route::get('vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
        Route::put('vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    });
    // Vendors - Delete
    Route::middleware(['permission:vendors.delete'])->group(function () {
        Route::delete('vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    });
    
    // Equipment Menu
    // Equipment - List
    Route::middleware(['permission:equipment,equipment.view'])->group(function () {
        Route::get('equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    });
    // Equipment - Create
    Route::middleware(['permission:equipment.create'])->group(function () {
        Route::get('equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    });
    // Equipment - View
    Route::middleware(['permission:equipment,equipment.view'])->group(function () {
        Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    });
    // Equipment - Assign
    Route::middleware(['permission:equipment.create'])->group(function () {
        Route::get('orders/{order}/assign-equipment', [EquipmentController::class, 'assignToEvent'])->name('equipment.assign');
        Route::post('orders/{order}/assign-equipment', [EquipmentController::class, 'storeAssignment'])->name('equipment.assign.store');
    });
    // Equipment - Edit
    Route::middleware(['permission:equipment.edit'])->group(function () {
        Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    });
    // Equipment - Delete
    Route::middleware(['permission:equipment.delete'])->group(function () {
        Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    });
    
    // Reports Menu
    // Reports - Orders, Payments, Expenses, Customers, Profit-Loss
    Route::middleware(['permission:reports,reports.view'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('orders', [ReportController::class, 'orders'])->name('orders');
        Route::get('payments', [ReportController::class, 'payments'])->name('payments');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
    });
    // Reports - Export
    Route::middleware(['permission:reports.export'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('export', [ReportController::class, 'export'])->name('export');
    });
    
    // Users Management Menu (Admin only)
    // Users - List
    Route::middleware(['permission:users,users.view'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
    // Users - Create
    Route::middleware(['permission:users.create'])->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    // Users - Edit
    Route::middleware(['permission:users.edit'])->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });
    // Users - Delete
    Route::middleware(['permission:users.delete'])->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Roles & Permissions Menu (Admin only)
    // Roles - List & Assign
    Route::middleware(['permission:roles,roles.view'])->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('users/{user}/assign-roles', [RoleController::class, 'assignRole'])->name('roles.assign');
    });
    // Roles - Create
    Route::middleware(['permission:roles.create'])->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    // Roles - Edit & Assign Store
    Route::middleware(['permission:roles.edit'])->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::post('users/{user}/assign-roles', [RoleController::class, 'storeRoleAssignment'])->name('roles.assign.store');
    });
    // Roles - Delete
    Route::middleware(['permission:roles.delete'])->group(function () {
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Global Search
    Route::get('search', [SearchController::class, 'search'])->name('search');
    
    // Staff Management Menu
    // Staff - List
    Route::middleware(['permission:staff,staff.view'])->group(function () {
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
    });
    // Staff - Create (must be before staff/{staff} route)
    Route::middleware(['permission:staff.create'])->group(function () {
        Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('orders/{order}/assign-staff', [StaffController::class, 'assignToEvent'])->name('staff.assign');
        Route::post('orders/{order}/assign-staff', [StaffController::class, 'storeAssignment'])->name('staff.assign.store');
    });
    // Staff - Edit (must be before staff/{staff} route)
    Route::middleware(['permission:staff.edit'])->group(function () {
        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('staff/{staff}/toggle', [StaffController::class, 'toggle'])->name('staff.toggle');
    });
    // Staff - View (specific routes before parameterized route)
    Route::middleware(['permission:staff,staff.view'])->group(function () {
        Route::get('staff/{staff}/workload', [StaffController::class, 'workload'])->name('staff.workload');
        Route::get('staff/{staff}/performance', [StaffController::class, 'performance'])->name('staff.performance');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    });
    // Staff - Delete
    Route::middleware(['permission:staff.delete'])->group(function () {
        Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });
    
    // Attendance Management Menu
    // Attendance - List, View & Report
    Route::middleware(['permission:attendance,attendance.view'])->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::get('attendance/staff/{staff}', [AttendanceController::class, 'staffHistory'])->name('attendance.staff');
    });
    // Attendance - Create
    Route::middleware(['permission:attendance.create'])->group(function () {
        Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('attendance/bulk', [AttendanceController::class, 'bulkCreate'])->name('attendance.bulk');
        Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk.store');
    });
    // Attendance - Edit
    Route::middleware(['permission:attendance.edit'])->group(function () {
        Route::get('attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    });
    
    // Settings Menu
    Route::prefix('settings')->name('settings.')->group(function () {
        // Settings - Order Statuses
        Route::get('order-statuses', [SettingsController::class, 'orderStatuses'])->name('order-statuses');
        Route::get('order-statuses/create', [SettingsController::class, 'createOrderStatus'])->name('order-statuses.create');
        Route::post('order-statuses', [SettingsController::class, 'storeOrderStatus'])->name('order-statuses.store');
        Route::get('order-statuses/{orderStatus}/edit', [SettingsController::class, 'editOrderStatus'])->name('order-statuses.edit');
        Route::put('order-statuses/{orderStatus}', [SettingsController::class, 'updateOrderStatus'])->name('order-statuses.update');
        Route::patch('order-statuses/{orderStatus}/toggle', [SettingsController::class, 'toggleOrderStatus'])->name('order-statuses.toggle');
        Route::delete('order-statuses/{orderStatus}', [SettingsController::class, 'destroyOrderStatus'])->name('order-statuses.destroy');
        
        // Settings - Event Times
        Route::get('event-times', [SettingsController::class, 'eventTimes'])->name('event-times');
        Route::get('event-times/create', [SettingsController::class, 'createEventTime'])->name('event-times.create');
        Route::post('event-times', [SettingsController::class, 'storeEventTime'])->name('event-times.store');
        Route::get('event-times/{eventTime}/edit', [SettingsController::class, 'editEventTime'])->name('event-times.edit');
        Route::put('event-times/{eventTime}', [SettingsController::class, 'updateEventTime'])->name('event-times.update');
        Route::patch('event-times/{eventTime}/toggle', [SettingsController::class, 'toggleEventTime'])->name('event-times.toggle');
        Route::delete('event-times/{eventTime}', [SettingsController::class, 'destroyEventTime'])->name('event-times.destroy');
        
        // Settings - Order Types
        Route::get('order-types', [SettingsController::class, 'orderTypes'])->name('order-types');
        Route::get('order-types/create', [SettingsController::class, 'createOrderType'])->name('order-types.create');
        Route::post('order-types', [SettingsController::class, 'storeOrderType'])->name('order-types.store');
        Route::get('order-types/{orderType}/edit', [SettingsController::class, 'editOrderType'])->name('order-types.edit');
        Route::put('order-types/{orderType}', [SettingsController::class, 'updateOrderType'])->name('order-types.update');
        Route::patch('order-types/{orderType}/toggle', [SettingsController::class, 'toggleOrderType'])->name('order-types.toggle');
        Route::delete('order-types/{orderType}', [SettingsController::class, 'destroyOrderType'])->name('order-types.destroy');
        
        // Settings - Inventory Units
        Route::get('inventory-units', [SettingsController::class, 'inventoryUnits'])->name('inventory-units');
        Route::get('inventory-units/create', [SettingsController::class, 'createInventoryUnit'])->name('inventory-units.create');
        Route::post('inventory-units', [SettingsController::class, 'storeInventoryUnit'])->name('inventory-units.store');
        Route::get('inventory-units/{inventoryUnit}/edit', [SettingsController::class, 'editInventoryUnit'])->name('inventory-units.edit');
        Route::put('inventory-units/{inventoryUnit}', [SettingsController::class, 'updateInventoryUnit'])->name('inventory-units.update');
        Route::patch('inventory-units/{inventoryUnit}/toggle', [SettingsController::class, 'toggleInventoryUnit'])->name('inventory-units.toggle');
        Route::delete('inventory-units/{inventoryUnit}', [SettingsController::class, 'destroyInventoryUnit'])->name('inventory-units.destroy');
        
        // Settings - Equipment Categories
        Route::get('equipment-categories', [SettingsController::class, 'equipmentCategories'])->name('equipment-categories');
        Route::get('equipment-categories/create', [SettingsController::class, 'createEquipmentCategory'])->name('equipment-categories.create');
        Route::post('equipment-categories', [SettingsController::class, 'storeEquipmentCategory'])->name('equipment-categories.store');
        Route::get('equipment-categories/{equipmentCategory}/edit', [SettingsController::class, 'editEquipmentCategory'])->name('equipment-categories.edit');
        Route::put('equipment-categories/{equipmentCategory}', [SettingsController::class, 'updateEquipmentCategory'])->name('equipment-categories.update');
        Route::patch('equipment-categories/{equipmentCategory}/toggle', [SettingsController::class, 'toggleEquipmentCategory'])->name('equipment-categories.toggle');
        Route::delete('equipment-categories/{equipmentCategory}', [SettingsController::class, 'destroyEquipmentCategory'])->name('equipment-categories.destroy');
        
        // Settings - Staff Roles
        Route::get('staff-roles', [\App\Http\Controllers\Settings\StaffRoleController::class, 'index'])->name('staff-roles');
        Route::get('staff-roles/create', [\App\Http\Controllers\Settings\StaffRoleController::class, 'create'])->name('staff-roles.create');
        Route::post('staff-roles', [\App\Http\Controllers\Settings\StaffRoleController::class, 'store'])->name('staff-roles.store');
        Route::get('staff-roles/{staffRole}/edit', [\App\Http\Controllers\Settings\StaffRoleController::class, 'edit'])->name('staff-roles.edit');
        Route::put('staff-roles/{staffRole}', [\App\Http\Controllers\Settings\StaffRoleController::class, 'update'])->name('staff-roles.update');
        Route::patch('staff-roles/{staffRole}/toggle', [\App\Http\Controllers\Settings\StaffRoleController::class, 'toggle'])->name('staff-roles.toggle');
        Route::delete('staff-roles/{staffRole}', [\App\Http\Controllers\Settings\StaffRoleController::class, 'destroy'])->name('staff-roles.destroy');
    });
});
 