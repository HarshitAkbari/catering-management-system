<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $isAdmin = $user->isAdmin();
        $isManager = $user->isManager();
        $isStaff = $user->isStaff();

        // Get staff model for staff users
        $staffModel = null;
        if ($isStaff) {
            $staffModel = $user->staff;
        }

        // Get status IDs by name for this tenant
        $confirmedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'confirmed')->value('id');
        $pendingStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'pending')->value('id');
        $completedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'completed')->value('id');
        
        $statusIds = array_filter([$pendingStatusId, $confirmedStatusId]);

        // Base query for orders - filter by staff assignment if staff user
        $baseOrderQuery = function() use ($tenantId, $isStaff, $staffModel) {
            $query = Order::where('tenant_id', $tenantId);
            if ($isStaff && $staffModel) {
                $query->whereHas('staff', function($q) use ($staffModel) {
                    $q->where('staff.id', $staffModel->id);
                });
            }
            return $query;
        };

        // Stats - role-specific
        $stats = [];
        
        if ($isAdmin || $isManager) {
            $stats['total_orders'] = $confirmedStatusId ? Order::where('tenant_id', $tenantId)->where('order_status_id', $confirmedStatusId)->count() : 0;
            $stats['total_customers'] = Customer::where('tenant_id', $tenantId)->count();
        }
        
        if ($isAdmin || $isManager || $isStaff) {
            $upcomingQuery = $baseOrderQuery();
            if (!empty($statusIds)) {
                $upcomingQuery->where('event_date', '>', today())
                    ->whereIn('order_status_id', $statusIds);
            }
            $stats['upcoming_events'] = !empty($statusIds) ? $upcomingQuery->count() : 0;
            
            $completedQuery = $baseOrderQuery();
            if ($completedStatusId) {
                $completedQuery->where('order_status_id', $completedStatusId);
            }
            $stats['completed_events'] = $completedStatusId ? $completedQuery->count() : 0;
        }

        if ($isAdmin) {
            $stats['pending_payments'] = Order::where('tenant_id', $tenantId)
                ->whereIn('payment_status', ['pending', 'partial'])
                ->count();
            $stats['this_month_revenue'] = Payment::where('tenant_id', $tenantId)
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount');
            $stats['total_revenue'] = Payment::where('tenant_id', $tenantId)->sum('amount');
        } elseif ($isManager) {
            $stats['pending_payments'] = Order::where('tenant_id', $tenantId)
                ->whereIn('payment_status', ['pending', 'partial'])
                ->count();
        }

        if ($isAdmin || $isManager) {
            $stats['low_stock_items'] = InventoryItem::where('tenant_id', $tenantId)
                ->whereRaw('current_stock <= minimum_stock')
                ->count();
        }

        if ($isStaff) {
            // Staff-specific stats
            $stats['upcoming_assignments'] = $stats['upcoming_events'] ?? 0;
            $todayTasksQuery = $baseOrderQuery();
            $todayTasksQuery->whereDate('event_date', today());
            $stats['today_tasks'] = $todayTasksQuery->count();
        }

        // Upcoming events
        $upcomingEvents = collect();
        if (!empty($statusIds)) {
            $upcomingQuery = $baseOrderQuery();
            $upcomingEvents = $upcomingQuery
                ->where('event_date', '>', today())
                ->whereIn('order_status_id', $statusIds)
                ->with('customer', 'orderStatus', 'eventTime')
                ->orderBy('event_date')
                ->limit(5)
                ->get();
        }

        // Today's deliveries
        $todayDeliveries = collect();
        if ($completedStatusId) {
            $todayQuery = $baseOrderQuery();
            $todayDeliveries = $todayQuery
                ->where('event_date', today())
                ->where('order_status_id', '!=', $completedStatusId)
                ->with('customer', 'orderStatus', 'eventTime')
                ->orderBy('event_time_id')
                ->get();
        }

        // Low stock items (Admin and Manager only)
        $lowStockItems = collect();
        $lowStockItemsCount = 0;
        if ($isAdmin || $isManager) {
            $lowStockItems = InventoryItem::where('tenant_id', $tenantId)
                ->whereRaw('current_stock <= minimum_stock')
                ->with('inventoryUnit')
                ->orderBy('current_stock', 'asc')
                ->limit(5)
                ->get();
            $lowStockItemsCount = $lowStockItems->count();
        }

        // Pending payments (Admin and Manager only)
        $pendingPayments = collect();
        if ($isAdmin || $isManager) {
            $pendingPayments = Order::where('tenant_id', $tenantId)
                ->whereIn('payment_status', ['pending', 'partial'])
                ->with('customer')
                ->orderBy('event_date')
                ->limit(5)
                ->get();
        }

        // Staff-related data (Admin only)
        $totalStaff = 0;
        $todayPresent = 0;
        $todayAbsent = 0;
        $upcomingStaffAssignments = collect();
        
        if ($isAdmin) {
            $totalStaff = Staff::where('tenant_id', $tenantId)->where('status', 'active')->count();
            $todayAttendance = Attendance::where('tenant_id', $tenantId)
                ->where('date', today())
                ->get();
            $todayPresent = $todayAttendance->where('status', 'present')->count();
            $todayAbsent = $todayAttendance->where('status', 'absent')->count();
            
            // Upcoming staff assignments (events with staff assigned in next 7 days)
            $upcomingStaffAssignments = Order::where('tenant_id', $tenantId)
                ->where('event_date', '>=', today())
                ->where('event_date', '<=', now()->addDays(7))
                ->whereHas('staff')
                ->with(['customer', 'staff', 'orderStatus', 'eventTime'])
                ->orderBy('event_date')
                ->limit(5)
                ->get();
        } elseif ($isManager) {
            // Manager sees upcoming staff assignments for events they manage
            $upcomingStaffAssignments = Order::where('tenant_id', $tenantId)
                ->where('event_date', '>=', today())
                ->where('event_date', '<=', now()->addDays(7))
                ->whereHas('staff')
                ->with(['customer', 'staff', 'orderStatus', 'eventTime'])
                ->orderBy('event_date')
                ->limit(5)
                ->get();
        }

        // Chart data - role-specific
        $chartData = [];
        if ($isAdmin) {
            $chartData = $this->getChartData($tenantId);
        } elseif ($isManager) {
            // Manager gets orders and payment status charts, but not revenue
            $chartData = $this->getChartDataForManager($tenantId);
        }
        // Staff gets no charts

        // Calendar events - filtered by role
        $calendarEvents = $this->getCalendarEvents($tenantId, $isStaff, $staffModel);

        // Upcoming schedule - filtered by role
        $upcomingSchedule = $this->getUpcomingSchedule($tenantId, $isStaff, $staffModel);

        // Today's deliveries - filtered by role
        $todayDeliveriesSchedule = $this->getTodayDeliveriesSchedule($tenantId, $isStaff, $staffModel);

        return view('dashboard', compact(
            'stats', 'upcomingEvents', 'todayDeliveries', 'lowStockItems', 'lowStockItemsCount', 
            'pendingPayments', 'chartData', 'totalStaff', 'todayPresent', 'todayAbsent', 
            'upcomingStaffAssignments', 'calendarEvents', 'upcomingSchedule', 'todayDeliveriesSchedule',
            'isAdmin', 'isManager', 'isStaff'
        ));
    }

    /**
     * Get chart data for manager (no revenue data)
     */
    private function getChartDataForManager(int $tenantId): array
    {
        // Orders over time - last 6 months
        $confirmedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'confirmed')->value('id');
        $completedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'completed')->value('id');
        
        $ordersData = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->with('orderStatus')
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($monthOrders) {
                return [
                    'confirmed' => $monthOrders->where('orderStatus.name', 'confirmed')->count(),
                    'completed' => $monthOrders->where('orderStatus.name', 'completed')->count(),
                ];
            });

        $orderMonths = [];
        $confirmedOrders = [];
        $completedOrders = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $orderMonths[] = now()->subMonths($i)->format('M Y');
            $monthData = $ordersData->get($month, ['confirmed' => 0, 'completed' => 0]);
            $confirmedOrders[] = $monthData['confirmed'];
            $completedOrders[] = $monthData['completed'];
        }

        // Payment status distribution (simplified - no amounts)
        $paymentStatusData = Order::where('tenant_id', $tenantId)
            ->selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->get();

        $paymentStatusLabels = [];
        $paymentStatusValues = [];
        foreach ($paymentStatusData as $item) {
            $paymentStatusLabels[] = ucfirst($item->payment_status ?? 'unknown');
            $paymentStatusValues[] = $item->count;
        }

        return [
            'orders_over_time' => [
                'labels' => $orderMonths,
                'confirmed' => $confirmedOrders,
                'completed' => $completedOrders,
            ],
            'payment_status' => [
                'labels' => $paymentStatusLabels,
                'data' => $paymentStatusValues,
            ],
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData(int $tenantId): array
    {
        // Revenue trend - last 6 months
        $revenueData = Payment::where('tenant_id', $tenantId)
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueLabels = [];
        $revenueValues = [];
        $months = [];
        
        // Generate last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $months[] = now()->subMonths($i)->format('M Y');
            $revenueLabels[] = $month;
            $revenueValues[] = $revenueData->firstWhere('month', $month)?->total ?? 0;
        }

        // Orders over time - last 6 months
        $confirmedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'confirmed')->value('id');
        $completedStatusId = OrderStatus::where('tenant_id', $tenantId)->where('name', 'completed')->value('id');
        
        $ordersData = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->with('orderStatus')
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($monthOrders) {
                return [
                    'confirmed' => $monthOrders->where('orderStatus.name', 'confirmed')->count(),
                    'completed' => $monthOrders->where('orderStatus.name', 'completed')->count(),
                ];
            });

        $orderMonths = [];
        $confirmedOrders = [];
        $completedOrders = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $orderMonths[] = now()->subMonths($i)->format('M Y');
            $monthData = $ordersData->get($month, ['confirmed' => 0, 'completed' => 0]);
            $confirmedOrders[] = $monthData['confirmed'];
            $completedOrders[] = $monthData['completed'];
        }

        // Payment status distribution
        $paymentStatusData = Order::where('tenant_id', $tenantId)
            ->selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->get();

        $paymentStatusLabels = [];
        $paymentStatusValues = [];
        foreach ($paymentStatusData as $item) {
            $paymentStatusLabels[] = ucfirst($item->payment_status ?? 'unknown');
            $paymentStatusValues[] = $item->count;
        }

        // Monthly revenue comparison (current vs previous month)
        $currentMonthRevenue = Payment::where('tenant_id', $tenantId)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $previousMonthRevenue = Payment::where('tenant_id', $tenantId)
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        return [
            'revenue_trend' => [
                'labels' => $months,
                'data' => $revenueValues,
            ],
            'orders_over_time' => [
                'labels' => $orderMonths,
                'confirmed' => $confirmedOrders,
                'completed' => $completedOrders,
            ],
            'payment_status' => [
                'labels' => $paymentStatusLabels,
                'data' => $paymentStatusValues,
            ],
            'monthly_comparison' => [
                'current' => (float) $currentMonthRevenue,
                'previous' => (float) $previousMonthRevenue,
                'labels' => [now()->subMonth()->format('M Y'), now()->format('M Y')],
            ],
        ];
    }

    /**
     * Get calendar events formatted for FullCalendar
     */
    private function getCalendarEvents(int $tenantId, bool $isStaff = false, $staffModel = null): array
    {
        $query = Order::where('tenant_id', $tenantId)
            ->whereNotNull('event_date');
        
        // Filter by staff assignment if staff user
        if ($isStaff && $staffModel) {
            $query->whereHas('staff', function($q) use ($staffModel) {
                $q->where('staff.id', $staffModel->id);
            });
        }
        
        $orders = $query->with('customer', 'orderStatus', 'eventTime')->get();

        $events = [];
        foreach ($orders as $order) {
            $statusName = $order->orderStatus?->name ?? 'pending';
            $customerName = $order->customer?->name ?? 'Unknown';
            
            // Determine color based on status
            $className = match($statusName) {
                'confirmed' => 'bg-primary',
                'completed' => 'bg-success',
                'cancelled' => 'bg-danger',
                default => 'bg-warning',
            };

            $events[] = [
                'title' => $customerName . ($order->event_menu ? ' - ' . $order->event_menu : ''),
                'start' => $order->event_date->format('Y-m-d'),
                'end' => $order->event_date->format('Y-m-d'),
                'url' => route('orders.show', $order->id),
                'className' => $className,
            ];
        }

        return $events;
    }

    /**
     * Get upcoming schedule data for widget
     */
    private function getUpcomingSchedule(int $tenantId, bool $isStaff = false, $staffModel = null): array
    {
        // Get all upcoming orders (future dates only, excluding today)
        $query = Order::where('tenant_id', $tenantId)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '>', today());
        
        // Filter by staff assignment if staff user
        if ($isStaff && $staffModel) {
            $query->whereHas('staff', function($q) use ($staffModel) {
                $q->where('staff.id', $staffModel->id);
            });
        }
        
        $orders = $query->with('customer', 'orderStatus', 'eventTime')
            ->orderBy('event_date')
            ->orderBy('event_time_id')
            ->limit(3)
            ->get();

        $schedule = [];
        $colors = ['bg-primary', 'bg-warning', 'bg-secondary'];
        $index = 0;

        foreach ($orders as $order) {
            $statusName = $order->orderStatus?->name ?? 'pending';
            $color = match($statusName) {
                'confirmed' => 'bg-primary',
                'pending' => 'bg-warning',
                default => $colors[$index % count($colors)],
            };

            $schedule[] = [
                'id' => $order->id,
                'title' => $order->event_menu ?? 'Event',
                'customer_name' => $order->customer?->name ?? 'Unknown',
                'customer_avatar' => null, // Can be added if customer avatars are implemented
                'date' => $order->event_date->format('F j, Y'),
                'time' => $order->eventTime?->name ?? 'N/A',
                'color' => $color,
                'url' => route('orders.show', $order->id),
            ];
            $index++;
        }

        return $schedule;
    }

    /**
     * Get today's deliveries data for widget
     */
    private function getTodayDeliveriesSchedule(int $tenantId, bool $isStaff = false, $staffModel = null): array
    {
        // Get all orders for today regardless of status (like calendar does)
        $query = Order::where('tenant_id', $tenantId)
            ->whereNotNull('event_date')
            ->whereDate('event_date', today());
        
        // Filter by staff assignment if staff user
        if ($isStaff && $staffModel) {
            $query->whereHas('staff', function($q) use ($staffModel) {
                $q->where('staff.id', $staffModel->id);
            });
        }
        
        $orders = $query->with('customer', 'orderStatus', 'eventTime')
            ->orderBy('event_time_id')
            ->limit(3)
            ->get();

        $schedule = [];
        $colors = ['bg-primary', 'bg-warning', 'bg-info'];
        $index = 0;

        foreach ($orders as $order) {
            $statusName = $order->orderStatus?->name ?? 'pending';
            $color = match($statusName) {
                'confirmed' => 'bg-primary',
                'pending' => 'bg-warning',
                default => $colors[$index % count($colors)],
            };

            $schedule[] = [
                'id' => $order->id,
                'title' => $order->event_menu ?? 'Event',
                'customer_name' => $order->customer?->name ?? 'Unknown',
                'customer_avatar' => null,
                'date' => $order->event_date->format('F j, Y'),
                'time' => $order->eventTime?->name ?? 'N/A',
                'color' => $color,
                'url' => route('orders.show', $order->id),
            ];
            $index++;
        }

        return $schedule;
    }
}
