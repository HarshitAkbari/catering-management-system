<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function orders(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::where('tenant_id', auth()->user()->tenant_id)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->with('customer', 'package')
            ->orderBy('event_date', 'desc')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('estimated_cost'),
            'confirmed' => $orders->where('status', 'confirmed')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
            'pending' => $orders->where('status', 'pending')->count(),
        ];

        return view('reports.orders', compact('orders', 'summary', 'startDate', 'endDate'));
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

        return view('reports.payments', compact('payments', 'summary', 'startDate', 'endDate'));
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

        return view('reports.expenses', compact('stockPurchases', 'summary', 'startDate', 'endDate'));
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

        return view('reports.customers', compact('customers', 'returningCustomers'));
    }

    public function staff(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->withCount(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('event_date', [$startDate, $endDate]);
            }])
            ->with(['attendance' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get();

        return view('reports.staff', compact('staff', 'startDate', 'endDate'));
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

        return view('reports.profit-loss', compact('revenue', 'expenses', 'profit', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'orders');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // This would typically use a package like Laravel Excel or DomPDF
        // For now, we'll just redirect back with a message
        return back()->with('info', 'Export functionality will be implemented with PDF/Excel packages.');
    }
}

