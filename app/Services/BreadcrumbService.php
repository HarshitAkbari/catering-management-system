<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Route;

class BreadcrumbService
{
    /**
     * Generate breadcrumbs from the current route.
     *
     * @return array<int, array{label: string, url: string|null, active: bool}>
     */
    public function generate(): array
    {
        $route = Route::current();
        
        if (!$route) {
            return [];
        }

        $routeName = $route->getName();
        
        // Skip breadcrumbs for public routes
        if (in_array($routeName, ['login', 'register', 'welcome', null], true)) {
            return [];
        }

        $breadcrumbs = [];
        
        // Always start with Dashboard
        $breadcrumbs[] = [
            'label' => 'Dashboard',
            'url' => route('dashboard'),
            'active' => false,
        ];

        // If we're on the dashboard, return early
        if ($routeName === 'dashboard') {
            $breadcrumbs[0]['active'] = true;
            return $breadcrumbs;
        }

        // Parse route name
        $segments = explode('.', $routeName);
        
        // Handle resource routes (e.g., orders.index, orders.create, orders.show)
        if (count($segments) === 2) {
            [$resource, $action] = $segments;
            
            // Add resource index breadcrumb
            $resourceLabel = $this->formatLabel($resource);
            $resourceRoute = "{$resource}.index";
            
            // Check if resource index route exists
            if (Route::has($resourceRoute)) {
                $breadcrumbs[] = [
                    'label' => $resourceLabel,
                    'url' => route($resourceRoute),
                    'active' => false,
                ];
            } else {
                // If no index route, just add the label without URL
                $breadcrumbs[] = [
                    'label' => $resourceLabel,
                    'url' => null,
                    'active' => false,
                ];
            }

            // Handle resource actions
            if (in_array($action, ['create', 'edit', 'show'], true)) {
                $actionLabel = $this->getActionLabel($action);
                $breadcrumbs[] = [
                    'label' => $actionLabel,
                    'url' => null,
                    'active' => true,
                ];
            } elseif ($action !== 'index') {
                // Custom action (e.g., calendar, maintenance)
                $actionLabel = $this->formatLabel($action);
                $breadcrumbs[] = [
                    'label' => $actionLabel,
                    'url' => null,
                    'active' => true,
                ];
            } else {
                // Index action - mark resource as active
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
            }
        }
        // Handle nested routes (e.g., settings.company-profile, inventory.stock-in, reports.orders)
        elseif (count($segments) >= 2) {
            $parent = $segments[0];
            $parentLabel = $this->formatLabel($parent);
            
            // Check if parent has an index route
            $parentIndexRoute = "{$parent}.index";
            if (Route::has($parentIndexRoute)) {
                $breadcrumbs[] = [
                    'label' => $parentLabel,
                    'url' => route($parentIndexRoute),
                    'active' => false,
                ];
            } else {
                // Try root route (e.g., settings/)
                $parentRootRoute = $parent;
                if (Route::has($parentRootRoute)) {
                    $breadcrumbs[] = [
                        'label' => $parentLabel,
                        'url' => route($parentRootRoute),
                        'active' => false,
                    ];
                } else {
                    // No route found, just add label
                    $breadcrumbs[] = [
                        'label' => $parentLabel,
                        'url' => null,
                        'active' => false,
                    ];
                }
            }

            // Handle remaining segments
            $remainingSegments = array_slice($segments, 1);
            $lastSegment = end($remainingSegments);
            
            foreach ($remainingSegments as $index => $segment) {
                $isLast = ($index === count($remainingSegments) - 1);
                $segmentLabel = $this->formatLabel($segment);
                
                // Try to build route name for this segment
                $segmentRoute = implode('.', array_slice($segments, 0, $index + 2));
                
                $breadcrumbs[] = [
                    'label' => $segmentLabel,
                    'url' => Route::has($segmentRoute) && !$isLast ? route($segmentRoute) : null,
                    'active' => $isLast,
                ];
            }
        }
        // Handle simple routes (fallback)
        else {
            $label = $this->formatLabel($routeName);
            $breadcrumbs[] = [
                'label' => $label,
                'url' => null,
                'active' => true,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Format a route segment into a human-readable label.
     *
     * @param string $segment
     * @return string
     */
    private function formatLabel(string $segment): string
    {
        // Handle special cases
        $specialCases = [
            'stock-in' => 'Stock In',
            'stock-out' => 'Stock Out',
            'low-stock' => 'Low Stock',
            'company-profile' => 'Company Profile',
            'invoice-branding' => 'Invoice Branding',
            'event-types' => 'Event Types',
            'profit-loss' => 'Profit & Loss',
        ];

        if (isset($specialCases[$segment])) {
            return $specialCases[$segment];
        }

        // Replace hyphens with spaces and capitalize words
        $label = str_replace('-', ' ', $segment);
        $label = ucwords($label);

        return $label;
    }

    /**
     * Get label for resource action.
     *
     * @param string $action
     * @return string
     */
    private function getActionLabel(string $action): string
    {
        return match ($action) {
            'create' => 'Create',
            'edit' => 'Edit',
            'show' => 'Details',
            default => ucfirst($action),
        };
    }
}
