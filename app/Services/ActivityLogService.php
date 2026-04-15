<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class ActivityLogService extends BaseService
{
    public function __construct(private readonly ActivityLogRepository $repository)
    {
        parent::__construct($repository);
    }

    public function logRequest(?Authenticatable $user, Request $request): void
    {
        try {
            $routeName = optional($request->route())->getName();
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => $routeName,
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'description' => $this->generateDescription($routeName, $request),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Avoid breaking the request cycle. Consider logging to a channel.
            report($e);
        }
    }

    public function logLogin(?Authenticatable $user, Request $request): void
    {
        try {
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => 'login',
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'description' => 'logged in to the system',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function logLogout(?Authenticatable $user, Request $request): void
    {
        try {
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => 'logout',
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'description' => 'logged out from the system',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Generate human-readable description from route name
     */
    private function generateDescription(?string $routeName, Request $request): string
    {
        if (!$routeName) {
            // Fallback to URL path if no route name
            $path = $request->path();
            return "visited {$path}";
        }

        // Comprehensive route patterns and their descriptions
        $descriptions = [
            // Dashboard
            'dashboard' => 'viewed dashboard',
            
            // Orders
            'orders.index' => 'viewed orders list',
            'orders.calendar' => 'viewed orders calendar',
            'orders.create' => 'viewed create order page',
            'orders.store' => 'created a new order',
            'orders.show' => 'viewed an order',
            'orders.edit' => 'viewed edit order page',
            'orders.update' => 'updated an order',
            'orders.update-status' => 'updated order status',
            'orders.destroy' => 'deleted an order',
            
            // Customers
            'customers.index' => 'viewed customers list',
            'customers.create' => 'viewed create customer page',
            'customers.store' => 'created a new customer',
            'customers.show' => 'viewed a customer',
            'customers.edit' => 'viewed edit customer page',
            'customers.update' => 'updated a customer',
            'customers.destroy' => 'deleted a customer',
            
            // Payments
            'payments.index' => 'viewed payments list',
            'payments.create' => 'viewed record payment page',
            'payments.store' => 'recorded a payment',
            'payments.show' => 'viewed a payment',
            'payments.edit' => 'viewed edit payment page',
            'payments.update' => 'updated a payment',
            'payments.update-group' => 'updated payment status',
            'payments.destroy' => 'deleted a payment',
            
            // Invoices
            'invoices.index' => 'viewed invoices list',
            'invoices.show' => 'viewed an invoice',
            'invoices.download' => 'downloaded an invoice',
            'invoices.generate' => 'generated an invoice',
            
            // Inventory
            'inventory.index' => 'viewed inventory',
            'inventory.low-stock' => 'viewed low stock items',
            'inventory.create' => 'viewed add inventory item page',
            'inventory.store' => 'added inventory item',
            'inventory.show' => 'viewed an inventory item',
            'inventory.edit' => 'viewed edit inventory item page',
            'inventory.update' => 'updated inventory item',
            'inventory.stock-in' => 'viewed stock in page',
            'inventory.stock-in.store' => 'added stock to inventory',
            'inventory.stock-out' => 'viewed stock out page',
            'inventory.stock-out.store' => 'removed stock from inventory',
            'inventory.destroy' => 'deleted inventory item',
            
            // Vendors
            'vendors.index' => 'viewed vendors list',
            'vendors.create' => 'viewed create vendor page',
            'vendors.store' => 'created a new vendor',
            'vendors.show' => 'viewed a vendor',
            'vendors.edit' => 'viewed edit vendor page',
            'vendors.update' => 'updated a vendor',
            'vendors.destroy' => 'deleted a vendor',
            
            // Equipment
            'equipment.index' => 'viewed equipment list',
            'equipment.create' => 'viewed create equipment page',
            'equipment.store' => 'created new equipment',
            'equipment.show' => 'viewed equipment details',
            'equipment.edit' => 'viewed edit equipment page',
            'equipment.update' => 'updated equipment',
            'equipment.assign' => 'viewed assign equipment page',
            'equipment.assign.store' => 'assigned equipment to event',
            'equipment.destroy' => 'deleted equipment',
            
            // Reports
            'reports.orders' => 'viewed orders report',
            'reports.payments' => 'viewed payments report',
            'reports.expenses' => 'viewed expenses report',
            'reports.customers' => 'viewed customers report',
            'reports.profit-loss' => 'viewed profit & loss report',
            'reports.attendance' => 'viewed attendance report',
            'reports.export' => 'exported report',
            
            // Staff
            'staff.index' => 'viewed staff list',
            'staff.show' => 'viewed staff member details',
            'staff.workload' => 'viewed staff workload',
            'staff.performance' => 'viewed staff performance',
            'staff.edit' => 'viewed edit staff page',
            'staff.update' => 'updated staff member',
            'staff.toggle' => 'toggled staff status',
            'staff.assign' => 'viewed assign staff page',
            'staff.assign.store' => 'assigned staff to event',
            'staff.destroy' => 'deleted staff member',
            
            // Attendance
            'attendance.index' => 'viewed attendance list',
            'attendance.create' => 'viewed create attendance page',
            'attendance.store' => 'recorded attendance',
            'attendance.bulk' => 'viewed bulk attendance page',
            'attendance.bulk.store' => 'recorded bulk attendance',
            'attendance.edit' => 'viewed edit attendance page',
            'attendance.update' => 'updated attendance',
            'attendance.staff' => 'viewed staff attendance history',
            
            // Users
            'users.index' => 'viewed users list',
            'users.create' => 'viewed create user page',
            'users.store' => 'created a new user',
            'users.edit' => 'viewed edit user page',
            'users.update' => 'updated a user',
            'users.toggle' => 'toggled user status',
            'users.destroy' => 'deleted a user',
            
            // Roles
            'roles.index' => 'viewed roles list',
            'roles.create' => 'viewed create role page',
            'roles.store' => 'created a new role',
            'roles.edit' => 'viewed edit role page',
            'roles.update' => 'updated a role',
            'roles.assign' => 'viewed assign role page',
            'roles.assign.store' => 'assigned role to user',
            'roles.destroy' => 'deleted a role',
            
            // Profile
            'profile.edit' => 'viewed edit profile page',
            'profile.update' => 'updated profile',
            'change-password' => 'viewed change password page',
            'change-password.update' => 'changed password',
            
            // Settings - Order Statuses
            'settings.order-statuses' => 'viewed order statuses settings',
            'settings.order-statuses.create' => 'viewed create order status page',
            'settings.order-statuses.store' => 'created a new order status',
            'settings.order-statuses.edit' => 'viewed edit order status page',
            'settings.order-statuses.update' => 'updated order status',
            'settings.order-statuses.toggle' => 'toggled order status',
            'settings.order-statuses.destroy' => 'deleted order status',
            
            // Settings - Event Times
            'settings.event-times' => 'viewed event times settings',
            'settings.event-times.create' => 'viewed create event time page',
            'settings.event-times.store' => 'created a new event time',
            'settings.event-times.edit' => 'viewed edit event time page',
            'settings.event-times.update' => 'updated event time',
            'settings.event-times.toggle' => 'toggled event time',
            'settings.event-times.destroy' => 'deleted event time',
            
            // Settings - Order Types
            'settings.order-types' => 'viewed order types settings',
            'settings.order-types.create' => 'viewed create order type page',
            'settings.order-types.store' => 'created a new order type',
            'settings.order-types.edit' => 'viewed edit order type page',
            'settings.order-types.update' => 'updated order type',
            'settings.order-types.toggle' => 'toggled order type',
            'settings.order-types.destroy' => 'deleted order type',
            
            // Settings - Inventory Units
            'settings.inventory-units' => 'viewed inventory units settings',
            'settings.inventory-units.create' => 'viewed create inventory unit page',
            'settings.inventory-units.store' => 'created a new inventory unit',
            'settings.inventory-units.edit' => 'viewed edit inventory unit page',
            'settings.inventory-units.update' => 'updated inventory unit',
            'settings.inventory-units.toggle' => 'toggled inventory unit',
            'settings.inventory-units.destroy' => 'deleted inventory unit',
            
            // Settings - Equipment Categories
            'settings.equipment-categories' => 'viewed equipment categories settings',
            'settings.equipment-categories.create' => 'viewed create equipment category page',
            'settings.equipment-categories.store' => 'created a new equipment category',
            'settings.equipment-categories.edit' => 'viewed edit equipment category page',
            'settings.equipment-categories.update' => 'updated equipment category',
            'settings.equipment-categories.toggle' => 'toggled equipment category',
            'settings.equipment-categories.destroy' => 'deleted equipment category',
            
            // Settings - Staff Roles
            'settings.staff-roles' => 'viewed staff roles settings',
            'settings.staff-roles.create' => 'viewed create staff role page',
            'settings.staff-roles.store' => 'created a new staff role',
            'settings.staff-roles.edit' => 'viewed edit staff role page',
            'settings.staff-roles.update' => 'updated staff role',
            'settings.staff-roles.toggle' => 'toggled staff role',
            'settings.staff-roles.destroy' => 'deleted staff role',
            
            // Search
            'search' => 'performed a search',
            
            // Auth
            'login' => 'logged in to the system',
            'loginpage' => 'viewed login page',
            'logout' => 'logged out from the system',
            'register' => 'viewed registration page',
            'password.request' => 'viewed forgot password page',
            'password.email' => 'requested password reset',
            'password.reset' => 'viewed reset password page',
            'password.update' => 'reset password',
            'lock-screen' => 'viewed lock screen',
        ];

        // Check for exact match first
        if (isset($descriptions[$routeName])) {
            return $descriptions[$routeName];
        }

        // Handle nested routes (e.g., settings.order-types.create)
        $parts = explode('.', $routeName);
        
        if (count($parts) >= 3) {
            // Handle settings.* routes
            if ($parts[0] === 'settings') {
                $resource = $this->formatResourceName($parts[1]);
                $action = $parts[2];
                
                $actionMap = [
                    'index' => "viewed {$resource} settings",
                    'create' => "viewed create {$resource} page",
                    'store' => "created a new {$resource}",
                    'show' => "viewed {$resource}",
                    'edit' => "viewed edit {$resource} page",
                    'update' => "updated {$resource}",
                    'toggle' => "toggled {$resource}",
                    'destroy' => "deleted {$resource}",
                ];
                
                if (isset($actionMap[$action])) {
                    return $actionMap[$action];
                }
            }
            
            // Handle other nested routes (e.g., inventory.stock-in.store)
            if (count($parts) === 3) {
                $resource = $this->formatResourceName($parts[0]);
                $subResource = $this->formatResourceName($parts[1]);
                $action = $parts[2];
                
                if ($action === 'store') {
                    return "performed {$subResource} for {$resource}";
                }
            }
        }
        
        // Try to parse simple route pattern (e.g., "orders.create" -> "created a new order")
        if (count($parts) >= 2) {
            $resource = $this->formatResourceName($parts[0]);
            $action = $parts[1];
            
            $actionMap = [
                'index' => "viewed {$resource} list",
                'create' => "viewed create {$resource} page",
                'store' => "created a new {$resource}",
                'show' => "viewed a {$resource}",
                'edit' => "viewed edit {$resource} page",
                'update' => "updated a {$resource}",
                'destroy' => "deleted a {$resource}",
                'toggle' => "toggled {$resource} status",
            ];
            
            if (isset($actionMap[$action])) {
                return $actionMap[$action];
            }
        }

        // Default fallback - convert route name to readable format
        $readable = $this->formatResourceName($routeName);
        return "visited {$readable}";
    }

    /**
     * Convert kebab-case or dot-separated route names to readable text
     * Examples: "order-types" -> "order types", "settings.order-types" -> "order types"
     */
    private function formatResourceName(string $name): string
    {
        // Remove settings prefix if present
        $name = str_replace('settings.', '', $name);
        
        // Split by dots and take the last part
        $parts = explode('.', $name);
        $name = end($parts);
        
        // Convert kebab-case to readable text
        $name = str_replace('-', ' ', $name);
        
        // Capitalize first letter of each word
        return ucwords($name);
    }
}

