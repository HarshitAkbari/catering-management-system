<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Get all orders (not filtered by payment_status)
        $allOrders = Order::with('customer')
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Separate orders with and without reference_number
        $ordersWithRef = $allOrders->filter(fn($order) => !empty($order->reference_number));
        $ordersWithoutRef = $allOrders->filter(fn($order) => empty($order->reference_number));
        
        // Group orders by reference_number
        $groupedOrders = $ordersWithRef->groupBy('reference_number')->map(function ($orderGroup, $referenceNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'reference_number' => $referenceNumber,
                'customer' => $firstOrder->customer,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
            ];
        })->values();
        
        // Add individual orders without reference_number as separate entries
        $individualOrders = $ordersWithoutRef->map(function ($order) {
            return [
                'reference_number' => null,
                'customer' => $order->customer,
                'total_amount' => $order->estimated_cost,
                'payment_status' => $order->payment_status,
                'orders' => collect([$order]),
                'created_at' => $order->created_at,
            ];
        });
        
        // Merge grouped and individual orders, then sort by created_at
        $allGroupedOrders = $groupedOrders->concat($individualOrders)
            ->sortByDesc('created_at')
            ->values();
        
        // Manual pagination
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $items = $allGroupedOrders->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $allGroupedOrders->count();
        
        // Create paginator manually
        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('payments.index', compact('orders'));
    }
    
    public function updateGroupPaymentStatus(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'required|string',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);
        
        $tenantId = auth()->user()->tenant_id;
        
        // Update all orders with same reference_number
        $updatedCount = Order::where('tenant_id', $tenantId)
            ->where('reference_number', $validated['reference_number'])
            ->update(['payment_status' => $validated['payment_status']]);
        
        return redirect()->route('payments.index')
            ->with('success', "Payment status updated to '{$validated['payment_status']}' for {$updatedCount} order(s).");
    }
    
    /**
     * Get group payment status - returns payment status if all orders have same status, otherwise "mixed"
     */
    private function getGroupPaymentStatus($orderGroup): string
    {
        $paymentStatuses = $orderGroup->pluck('payment_status')->unique()->filter();
        return $paymentStatuses->count() === 1 ? $paymentStatuses->first() : 'mixed';
    }
}
