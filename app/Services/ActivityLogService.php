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

        // Common route patterns and their descriptions
        $descriptions = [
            // Orders
            'orders.index' => 'viewed orders list',
            'orders.create' => 'created a new order',
            'orders.store' => 'created a new order',
            'orders.show' => 'viewed an order',
            'orders.edit' => 'edited an order',
            'orders.update' => 'updated an order',
            'orders.destroy' => 'deleted an order',
            
            // Customers
            'customers.index' => 'viewed customers list',
            'customers.create' => 'created a new customer',
            'customers.store' => 'created a new customer',
            'customers.show' => 'viewed a customer',
            'customers.edit' => 'edited a customer',
            'customers.update' => 'updated a customer',
            'customers.destroy' => 'deleted a customer',
            
            // Payments
            'payments.index' => 'viewed payments list',
            'payments.create' => 'recorded a payment',
            'payments.store' => 'recorded a payment',
            'payments.show' => 'viewed a payment',
            'payments.edit' => 'edited a payment',
            'payments.update' => 'updated a payment',
            
            // Inventory
            'inventory.index' => 'viewed inventory',
            'inventory.create' => 'added inventory item',
            'inventory.store' => 'added inventory item',
            'inventory.edit' => 'edited inventory item',
            'inventory.update' => 'updated inventory item',
            'inventory.stock-in' => 'added stock to inventory',
            'inventory.stock-out' => 'removed stock from inventory',
            
            // Staff
            'staff.index' => 'viewed staff list',
            'staff.create' => 'created a new staff member',
            'staff.store' => 'created a new staff member',
            'staff.show' => 'viewed a staff member',
            'staff.edit' => 'edited a staff member',
            'staff.update' => 'updated a staff member',
            
            // Dashboard
            'dashboard' => 'viewed dashboard',
            
            // Auth
            'login' => 'logged in to the system',
            'logout' => 'logged out from the system',
        ];

        // Check for exact match first
        if (isset($descriptions[$routeName])) {
            return $descriptions[$routeName];
        }

        // Try to parse route pattern (e.g., "orders.create" -> "created an order")
        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            $resource = $parts[0];
            $action = $parts[1];
            
            $actionMap = [
                'index' => "viewed {$resource} list",
                'create' => "created a new {$resource}",
                'store' => "created a new {$resource}",
                'show' => "viewed a {$resource}",
                'edit' => "edited a {$resource}",
                'update' => "updated a {$resource}",
                'destroy' => "deleted a {$resource}",
            ];
            
            if (isset($actionMap[$action])) {
                return $actionMap[$action];
            }
        }

        // Default fallback
        return "visited {$routeName}";
    }
}

