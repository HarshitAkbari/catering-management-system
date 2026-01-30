<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->seedMenusForTenant($tenant);
        }
    }

    /**
     * Seed menus for a specific tenant.
     */
    private function seedMenusForTenant(Tenant $tenant): void
    {
        $order = 1;

        // Dashboard (parent)
        $dashboard = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'dashboard',
            ],
            [
                'display_name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'bi bi-grid',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        // Orders (parent)
        $orders = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'orders',
            ],
            [
                'display_name' => 'Orders',
                'route' => null,
                'icon' => 'bi bi-file-text',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        // Orders children
        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'orders.list',
            ],
            [
                'display_name' => 'Orders',
                'route' => 'orders.index',
                'icon' => null,
                'parent_id' => $orders->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'orders.calendar',
            ],
            [
                'display_name' => 'Order Calendar',
                'route' => 'orders.calendar',
                'icon' => null,
                'parent_id' => $orders->id,
                'order' => 2,
                'is_active' => true,
            ]
        );

        // Customers (parent)
        $customers = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'customers',
            ],
            [
                'display_name' => 'Customers',
                'route' => null,
                'icon' => 'bi bi-people',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'customers.list',
            ],
            [
                'display_name' => 'Customer List',
                'route' => 'customers.index',
                'icon' => null,
                'parent_id' => $customers->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Payments (parent)
        $payments = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'payments',
            ],
            [
                'display_name' => 'Payments',
                'route' => null,
                'icon' => 'bi bi-credit-card',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'payments.list',
            ],
            [
                'display_name' => 'Payments List',
                'route' => 'payments.index',
                'icon' => null,
                'parent_id' => $payments->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Inventory (parent)
        $inventory = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'inventory',
            ],
            [
                'display_name' => 'Inventory',
                'route' => null,
                'icon' => 'bi bi-box-seam',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'inventory.list',
            ],
            [
                'display_name' => 'Item List',
                'route' => 'inventory.index',
                'icon' => null,
                'parent_id' => $inventory->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'inventory.stock-in',
            ],
            [
                'display_name' => 'Stock In',
                'route' => 'inventory.stock-in',
                'icon' => null,
                'parent_id' => $inventory->id,
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'inventory.stock-out',
            ],
            [
                'display_name' => 'Stock Out',
                'route' => 'inventory.stock-out',
                'icon' => null,
                'parent_id' => $inventory->id,
                'order' => 3,
                'is_active' => true,
            ]
        );

        // Vendors (parent)
        $vendors = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'vendors',
            ],
            [
                'display_name' => 'Vendors',
                'route' => null,
                'icon' => 'bi bi-building',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'vendors.list',
            ],
            [
                'display_name' => 'Vendor List',
                'route' => 'vendors.index',
                'icon' => null,
                'parent_id' => $vendors->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Equipment (parent)
        $equipment = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'equipment',
            ],
            [
                'display_name' => 'Equipment',
                'route' => null,
                'icon' => 'bi bi-tools',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'equipment.list',
            ],
            [
                'display_name' => 'Equipment List',
                'route' => 'equipment.index',
                'icon' => null,
                'parent_id' => $equipment->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Staff (parent)
        $staff = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'staff',
            ],
            [
                'display_name' => 'Staff',
                'route' => null,
                'icon' => 'bi bi-people-fill',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'staff.list',
            ],
            [
                'display_name' => 'Staff List',
                'route' => 'staff.index',
                'icon' => null,
                'parent_id' => $staff->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        // Attendance (parent)
        $attendance = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'attendance',
            ],
            [
                'display_name' => 'Attendance',
                'route' => null,
                'icon' => 'bi bi-calendar-check',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'attendance.list',
            ],
            [
                'display_name' => 'Attendance List',
                'route' => 'attendance.index',
                'icon' => null,
                'parent_id' => $attendance->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'attendance.create',
            ],
            [
                'display_name' => 'Mark Attendance',
                'route' => 'attendance.create',
                'icon' => null,
                'parent_id' => $attendance->id,
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'attendance.bulk',
            ],
            [
                'display_name' => 'Bulk Mark',
                'route' => 'attendance.bulk',
                'icon' => null,
                'parent_id' => $attendance->id,
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'attendance.report',
            ],
            [
                'display_name' => 'Attendance Report',
                'route' => 'attendance.report',
                'icon' => null,
                'parent_id' => $attendance->id,
                'order' => 4,
                'is_active' => true,
            ]
        );

        // Reports (parent)
        $reports = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports',
            ],
            [
                'display_name' => 'Reports',
                'route' => null,
                'icon' => 'bi bi-graph-up',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.orders',
            ],
            [
                'display_name' => 'Orders',
                'route' => 'reports.orders',
                'icon' => null,
                'parent_id' => $reports->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.payments',
            ],
            [
                'display_name' => 'Payments',
                'route' => 'reports.payments',
                'icon' => null,
                'parent_id' => $reports->id,
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.expenses',
            ],
            [
                'display_name' => 'Expenses',
                'route' => 'reports.expenses',
                'icon' => null,
                'parent_id' => $reports->id,
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.customers',
            ],
            [
                'display_name' => 'Customers',
                'route' => 'reports.customers',
                'icon' => null,
                'parent_id' => $reports->id,
                'order' => 4,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'reports.profit-loss',
            ],
            [
                'display_name' => 'Profit & Loss',
                'route' => 'reports.profit-loss',
                'icon' => null,
                'parent_id' => $reports->id,
                'order' => 5,
                'is_active' => true,
            ]
        );

        // Settings (parent)
        $settings = Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings',
            ],
            [
                'display_name' => 'Settings',
                'route' => null,
                'icon' => 'bi bi-gear',
                'parent_id' => null,
                'order' => $order++,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.users',
            ],
            [
                'display_name' => 'User Management',
                'route' => 'users.index',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.order-statuses',
            ],
            [
                'display_name' => 'Order Statuses',
                'route' => 'settings.order-statuses',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.event-times',
            ],
            [
                'display_name' => 'Order Event Times',
                'route' => 'settings.event-times',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.order-types',
            ],
            [
                'display_name' => 'Order Types',
                'route' => 'settings.order-types',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 4,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.inventory-units',
            ],
            [
                'display_name' => 'Inventory Units',
                'route' => 'settings.inventory-units',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 5,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.equipment-categories',
            ],
            [
                'display_name' => 'Equipment Categories',
                'route' => 'settings.equipment-categories',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 6,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.staff-roles',
            ],
            [
                'display_name' => 'Staff Roles',
                'route' => 'settings.staff-roles',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 7,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'settings.roles',
            ],
            [
                'display_name' => 'Roles & Permissions',
                'route' => 'roles.index',
                'icon' => null,
                'parent_id' => $settings->id,
                'order' => 8,
                'is_active' => true,
            ]
        );
    }
}
