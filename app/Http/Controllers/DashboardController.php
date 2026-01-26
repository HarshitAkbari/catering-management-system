<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $stats = [
            'total_orders' => Order::where('tenant_id', $tenantId)->where('status', 'confirmed')->count(),
            'upcoming_events' => Order::where('tenant_id', $tenantId)
                ->where('event_date', '>', today())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'pending_payments' => Order::where('tenant_id', $tenantId)
                ->whereIn('payment_status', ['pending', 'partial'])
                ->count(),
            'completed_events' => Order::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->count(),
            'total_customers' => Customer::where('tenant_id', $tenantId)->count(),
            'this_month_revenue' => Payment::where('tenant_id', $tenantId)
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'total_revenue' => Payment::where('tenant_id', $tenantId)->sum('amount'),
        ];

        $upcomingEvents = Order::where('tenant_id', $tenantId)
            ->where('event_date', '>', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('customer')
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $todayDeliveries = Order::where('tenant_id', $tenantId)
            ->where('event_date', today())
            ->where('status', '!=', 'completed')
            ->with('customer')
            ->orderBy('event_time')
            ->get();

        $lowStockItems = InventoryItem::where('tenant_id', $tenantId)
            ->whereRaw('current_stock <= minimum_stock')
            ->count();

        $pendingPayments = Order::where('tenant_id', $tenantId)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->with('customer')
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        // Chart data
        $chartData = $this->getChartData($tenantId);

        return view('dashboard', compact('stats', 'upcomingEvents', 'todayDeliveries', 'lowStockItems', 'pendingPayments', 'chartData'));
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
        $ordersData = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, status')
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        $orderMonths = [];
        $confirmedOrders = [];
        $completedOrders = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $orderMonths[] = now()->subMonths($i)->format('M Y');
            $monthData = $ordersData->where('month', $month);
            $confirmedOrders[] = $monthData->where('status', 'confirmed')->sum('count');
            $completedOrders[] = $monthData->where('status', 'completed')->sum('count');
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
}
