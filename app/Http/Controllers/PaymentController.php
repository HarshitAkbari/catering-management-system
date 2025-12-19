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
        
        // Group orders by order_number
        $groupedOrders = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'order_number' => $orderNumber,
                'customer' => $firstOrder->customer,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
            ];
        })->values();
        
        // Sort by created_at
        $allGroupedOrders = $groupedOrders
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
            'order_number' => 'required|string',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);
        
        $tenantId = auth()->user()->tenant_id;
        
        // Update all orders with same order_number
        $updatedCount = Order::where('tenant_id', $tenantId)
            ->where('order_number', $validated['order_number'])
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
