<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load('orders.invoice.payments');
        
        // Get all orders for this customer
        $allOrders = $customer->orders;
        
        // Group orders by order_number
        $groupedOrdersList = $allOrders->groupBy('order_number')->map(function ($orderGroup, $orderNumber) {
            $firstOrder = $orderGroup->first();
            return [
                'order_number' => $orderNumber,
                'total_amount' => $orderGroup->sum('estimated_cost'),
                'status' => $this->getGroupStatus($orderGroup),
                'payment_status' => $this->getGroupPaymentStatus($orderGroup),
                'orders' => $orderGroup,
                'created_at' => $firstOrder->created_at,
                'event_date' => $orderGroup->min('event_date'),
            ];
        })->values()
            ->sortByDesc('created_at')
            ->values();
        
        return view('customers.show', compact('customer', 'groupedOrdersList'));
    }
    
    /**
     * Get group status - returns status if all orders have same status, otherwise "mixed"
     */
    private function getGroupStatus($orderGroup): string
    {
        $statuses = $orderGroup->pluck('status')->unique()->filter();
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
}
