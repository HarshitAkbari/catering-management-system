<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\ExpensesExport;
use App\Exports\OrdersExport;
use App\Exports\PaymentsExport;
use App\Exports\ProfitLossExport;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function orders(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Get all orders with relationships
        $allOrders = Order::where('tenant_id', auth()->user()->tenant_id)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->with('customer', 'orderStatus', 'orderType')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group orders by order_number
        $groupedOrders = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'order_number' => $orderNumber,
                'customer' => $firstOrder->customer,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'status' => $this->getGroupStatus($orderGroup),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
                'event_date' => $orderGroup->min('event_date'),
            ];
        })->values();

        // Sort by created_at
        $orders = $groupedOrders
            ->sortByDesc('created_at')
            ->values();

        // Calculate summary based on grouped orders
        $summary = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('total_amount'),
            'confirmed' => $allOrders->where('orderStatus.name', 'confirmed')->count(),
            'completed' => $allOrders->where('orderStatus.name', 'completed')->count(),
            'pending' => $allOrders->where('orderStatus.name', 'pending')->count(),
        ];

        // Chart data for orders
        $chartData = $this->getOrdersChartData(auth()->user()->tenant_id, $startDate, $endDate, $allOrders);

        return view('reports.orders', compact('orders', 'summary', 'startDate', 'endDate', 'chartData'));
    }

    public function payments(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $payments = Payment::where('tenant_id', auth()->user()->tenant_id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('invoice.order')
            ->orderBy('payment_date', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'cash' => $payments->where('payment_mode', 'cash')->sum('amount'),
            'upi' => $payments->where('payment_mode', 'upi')->sum('amount'),
            'bank_transfer' => $payments->where('payment_mode', 'bank_transfer')->sum('amount'),
        ];

        // Chart data for payments
        $chartData = $this->getPaymentsChartData(auth()->user()->tenant_id, $startDate, $endDate);

        return view('reports.payments', compact('payments', 'summary', 'startDate', 'endDate', 'chartData'));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $stockPurchases = StockTransaction::where('tenant_id', auth()->user()->tenant_id)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('inventoryItem', 'vendor')
            ->get();

        $summary = [
            'total_purchases' => $stockPurchases->count(),
            'total_amount' => $stockPurchases->sum('price'),
            'by_vendor' => $stockPurchases->groupBy('vendor_id')->map(function ($transactions) {
                return $transactions->sum('price');
            }),
        ];

        // Chart data for expenses
        $chartData = $this->getExpensesChartData(auth()->user()->tenant_id, $startDate, $endDate, $stockPurchases);

        return view('reports.expenses', compact('stockPurchases', 'summary', 'startDate', 'endDate', 'chartData'));
    }

    public function customers(Request $request)
    {
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('orders')
            ->withSum('orders', 'estimated_cost')
            ->having('orders_count', '>', 0)
            ->orderBy('orders_count', 'desc')
            ->get();

        $returningCustomers = $customers->where('orders_count', '>', 1);

        // Chart data for customers
        $chartData = $this->getCustomersChartData($customers);

        return view('reports.customers', compact('customers', 'returningCustomers', 'chartData'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $revenue = Payment::where('tenant_id', auth()->user()->tenant_id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = StockTransaction::where('tenant_id', auth()->user()->tenant_id)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price');

        $profit = $revenue - $expenses;

        // Chart data for profit/loss
        $chartData = $this->getProfitLossChartData(auth()->user()->tenant_id, $startDate, $endDate);

        return view('reports.profit-loss', compact('revenue', 'expenses', 'profit', 'startDate', 'endDate', 'chartData'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'orders');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $tenantId = auth()->user()->tenant_id;

        switch ($type) {
            case 'orders':
                return $this->exportOrders($tenantId, $startDate, $endDate);
            case 'payments':
                return $this->exportPayments($tenantId, $startDate, $endDate);
            case 'expenses':
                return $this->exportExpenses($tenantId, $startDate, $endDate);
            case 'profit-loss':
                return $this->exportProfitLoss($tenantId, $startDate, $endDate);
            default:
                return back()->with('error', 'Invalid export type.');
        }
    }

    private function exportOrders(int $tenantId, string $startDate, string $endDate)
    {
        // Get first order ID for each order_number to use for relationships
        $firstOrderIds = Order::where('tenant_id', $tenantId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->selectRaw('order_number, MIN(id) as first_order_id')
            ->groupBy('order_number')
            ->pluck('first_order_id');

        // Build subquery for total_amount
        $totalAmountSubquery = Order::where('tenant_id', $tenantId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->selectRaw('order_number, SUM(estimated_cost) as total_amount')
            ->groupBy('order_number');

        // Query first orders with relationships and join total_amount
        $query = Order::whereIn('id', $firstOrderIds)
            ->with(['customer', 'orderStatus', 'orderType', 'eventTime'])
            ->leftJoinSub($totalAmountSubquery, 'totals', function ($join) {
                $join->on('totals.order_number', '=', 'orders.order_number');
            })
            ->select('orders.*', 'totals.total_amount')
            ->orderBy('orders.created_at', 'desc');

        $filename = 'orders_' . $startDate . '_to_' . $endDate . '.xlsx';
        return Excel::download(new OrdersExport($query), $filename);
    }

    private function exportPayments(int $tenantId, string $startDate, string $endDate)
    {
        $query = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with('invoice.order')
            ->orderBy('payment_date', 'desc');

        $filename = 'payments_' . $startDate . '_to_' . $endDate . '.xlsx';
        return Excel::download(new PaymentsExport($query), $filename);
    }

    private function exportExpenses(int $tenantId, string $startDate, string $endDate)
    {
        $query = StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('inventoryItem', 'vendor');

        $filename = 'expenses_' . $startDate . '_to_' . $endDate . '.xlsx';
        return Excel::download(new ExpensesExport($query), $filename);
    }

    private function exportProfitLoss(int $tenantId, string $startDate, string $endDate)
    {
        $revenue = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price');

        $profit = $revenue - $expenses;

        $filename = 'profit_loss_' . $startDate . '_to_' . $endDate . '.xlsx';
        return Excel::download(new ProfitLossExport($revenue, $expenses, $profit, $startDate, $endDate), $filename);
    }

    /**
     * Get group status - returns status if all orders have same status, otherwise "mixed"
     */
    private function getGroupStatus($orderGroup): string
    {
        $statuses = $orderGroup->pluck('orderStatus.name')->unique()->filter();
        return $statuses->count() === 1 ? $statuses->first() : 'mixed';
    }

    /**
     * Get group payment status - returns payment status if all orders have same status, otherwise "mixed"
     */
    private function getGroupPaymentStatus($orderGroup): string
    {
        $paymentStatuses = $orderGroup->pluck('payment_status')->unique()->filter();
        return $paymentStatuses->count() === 1 ? $paymentStatuses->first() : 'mixed';
    }

    /**
     * Get chart data for payments report
     */
    private function getPaymentsChartData(int $tenantId, string $startDate, string $endDate): array
    {
        // Payment trends over time (daily)
        $paymentTrends = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = [];
        $trendData = [];
        foreach ($paymentTrends as $trend) {
            $trendLabels[] = \Carbon\Carbon::parse($trend->date)->format('M d');
            $trendData[] = (float) $trend->total;
        }

        // Payment method distribution
        $paymentMethods = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('payment_mode, SUM(amount) as total')
            ->groupBy('payment_mode')
            ->get();

        $methodLabels = [];
        $methodData = [];
        foreach ($paymentMethods as $method) {
            $methodLabels[] = ucfirst(str_replace('_', ' ', $method->payment_mode));
            $methodData[] = (float) $method->total;
        }

        return [
            'trends' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'methods' => [
                'labels' => $methodLabels,
                'data' => $methodData,
            ],
        ];
    }

    /**
     * Get chart data for expenses report
     */
    private function getExpensesChartData(int $tenantId, string $startDate, string $endDate, $stockPurchases): array
    {
        // Expense trends over time (daily)
        $expenseTrends = \App\Models\StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = [];
        $trendData = [];
        foreach ($expenseTrends as $trend) {
            $trendLabels[] = \Carbon\Carbon::parse($trend->date)->format('M d');
            $trendData[] = (float) $trend->total;
        }

        // Expenses by vendor
        $vendorExpenses = $stockPurchases->groupBy('vendor_id')->map(function ($transactions) {
            return $transactions->sum('price');
        })->sortDesc()->take(10);

        $vendorLabels = [];
        $vendorData = [];
        foreach ($vendorExpenses as $vendorId => $total) {
            $vendor = \App\Models\Vendor::find($vendorId);
            $vendorLabels[] = $vendor ? $vendor->name : 'Unknown';
            $vendorData[] = (float) $total;
        }

        // Monthly expense comparison
        $currentMonthExpenses = \App\Models\StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('price');

        $previousMonthExpenses = \App\Models\StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('price');

        return [
            'trends' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'vendors' => [
                'labels' => $vendorLabels,
                'data' => $vendorData,
            ],
            'monthly_comparison' => [
                'current' => (float) $currentMonthExpenses,
                'previous' => (float) $previousMonthExpenses,
                'labels' => [now()->subMonth()->format('M Y'), now()->format('M Y')],
            ],
        ];
    }

    /**
     * Get chart data for profit/loss report
     */
    private function getProfitLossChartData(int $tenantId, string $startDate, string $endDate): array
    {
        // Revenue vs Expenses comparison (monthly)
        $revenueData = Payment::where('tenant_id', $tenantId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $expenseData = \App\Models\StockTransaction::where('tenant_id', $tenantId)
            ->where('type', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get all unique months
        $allMonths = collect($revenueData->pluck('month'))->merge($expenseData->pluck('month'))->unique()->sort()->values();

        $monthLabels = [];
        $revenueValues = [];
        $expenseValues = [];
        $profitValues = [];

        foreach ($allMonths as $month) {
            $monthLabels[] = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $revenue = (float) ($revenueData->firstWhere('month', $month)?->total ?? 0);
            $expense = (float) ($expenseData->firstWhere('month', $month)?->total ?? 0);
            $revenueValues[] = $revenue;
            $expenseValues[] = $expense;
            $profitValues[] = $revenue - $expense;
        }

        return [
            'comparison' => [
                'labels' => $monthLabels,
                'revenue' => $revenueValues,
                'expenses' => $expenseValues,
            ],
            'profit_trend' => [
                'labels' => $monthLabels,
                'data' => $profitValues,
            ],
        ];
    }

    /**
     * Get chart data for orders report
     */
    private function getOrdersChartData(int $tenantId, string $startDate, string $endDate, $allOrders): array
    {
        // Order trends over time (daily)
        $orderTrends = Order::where('tenant_id', $tenantId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->selectRaw('DATE(event_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = [];
        $trendData = [];
        foreach ($orderTrends as $trend) {
            $trendLabels[] = \Carbon\Carbon::parse($trend->date)->format('M d');
            $trendData[] = (int) $trend->count;
        }

        // Order status distribution
        $statusData = $allOrders->groupBy(function ($order) {
            return $order->orderStatus ? $order->orderStatus->name : 'unknown';
        })->map(function ($group) {
            return $group->count();
        });

        $statusLabels = [];
        $statusValues = [];
        foreach ($statusData as $status => $count) {
            $statusLabels[] = ucfirst($status ?? 'unknown');
            $statusValues[] = $count;
        }

        // Orders by event type
        $eventTypeData = $allOrders->groupBy(function ($order) {
            return $order->orderType ? $order->orderType->name : 'unknown';
        })->map(function ($group) {
            return $group->count();
        })->sortDesc()->take(10);

        $eventTypeLabels = [];
        $eventTypeValues = [];
        foreach ($eventTypeData as $type => $count) {
            $eventTypeLabels[] = $type ? ucfirst($type) : 'Unknown';
            $eventTypeValues[] = $count;
        }

        return [
            'trends' => [
                'labels' => $trendLabels,
                'data' => $trendData,
            ],
            'status' => [
                'labels' => $statusLabels,
                'data' => $statusValues,
            ],
            'event_types' => [
                'labels' => $eventTypeLabels,
                'data' => $eventTypeValues,
            ],
        ];
    }

    /**
     * Get chart data for customers report
     */
    private function getCustomersChartData($customers): array
    {
        // Top customers by order count
        $topCustomers = $customers->sortByDesc('orders_count')->take(10);

        $customerLabels = [];
        $orderCounts = [];
        foreach ($topCustomers as $customer) {
            $customerLabels[] = $customer->name;
            $orderCounts[] = $customer->orders_count;
        }

        // Customer order frequency distribution
        $frequencyDistribution = [
            '1 order' => $customers->where('orders_count', 1)->count(),
            '2-5 orders' => $customers->where('orders_count', '>=', 2)->where('orders_count', '<=', 5)->count(),
            '6-10 orders' => $customers->where('orders_count', '>=', 6)->where('orders_count', '<=', 10)->count(),
            '11+ orders' => $customers->where('orders_count', '>', 10)->count(),
        ];

        $frequencyLabels = [];
        $frequencyData = [];
        foreach ($frequencyDistribution as $label => $count) {
            $frequencyLabels[] = $label;
            $frequencyData[] = $count;
        }

        return [
            'top_customers' => [
                'labels' => $customerLabels,
                'data' => $orderCounts,
            ],
            'frequency' => [
                'labels' => $frequencyLabels,
                'data' => $frequencyData,
            ],
        ];
    }
}

