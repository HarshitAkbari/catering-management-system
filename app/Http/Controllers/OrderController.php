<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Get all orders with customer relationship
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
                'status' => $this->getGroupStatus($orderGroup),
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
                'status' => $order->status,
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

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        // Decode events JSON if it's a string and merge back into request
        $eventsData = $request->input('events');
        if (is_string($eventsData)) {
            $decodedEvents = json_decode($eventsData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedEvents)) {
                $request->merge(['events' => $decodedEvents]);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['events' => 'Invalid events data. Please add at least one event.']);
            }
        }
        
        // Check if events data is valid
        if (empty($eventsData) || (is_array($eventsData) && count($eventsData) === 0)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['events' => 'Please add at least one event before submitting.']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'events' => 'required|array|min:1',
            'events.*.event_date' => 'required|date',
            'events.*.event_time' => 'required|in:morning,afternoon,evening,night_snack',
            'events.*.event_menu' => 'required|string|max:255',
            'events.*.guest_count' => 'required|integer|min:1',
            'events.*.order_type' => 'nullable|in:full_service,preparation_only',
            'events.*.dish_price' => 'required|numeric|min:0',
            'events.*.cost' => 'required|numeric|min:0',
        ]);

        $tenantId = auth()->user()->tenant_id;

        // Find or create customer
        $customer = Customer::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'mobile' => $validated['customer_mobile'],
            ],
            [
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
            ]
        );

        // Update customer name and email if different
        $updateData = [];
        if ($customer->name !== $validated['customer_name']) {
            $updateData['name'] = $validated['customer_name'];
        }
        if ($customer->email !== $validated['customer_email']) {
            $updateData['email'] = $validated['customer_email'];
        }
        if (!empty($updateData)) {
            $customer->update($updateData);
        }

        // Generate unique reference number
        $referenceNumber = 'REF-' . strtoupper(Str::random(8));
        while (Order::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('reference_number', $referenceNumber)->exists()) {
            $referenceNumber = 'REF-' . strtoupper(Str::random(8));
        }

        // Create orders for each event
        $createdOrders = [];
        foreach ($validated['events'] as $event) {
            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(8));
            while (Order::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'ORD-' . strtoupper(Str::random(8));
            }

            try {
                $order = Order::create([
                    'tenant_id' => $tenantId,
                    'customer_id' => $customer->id,
                    'order_number' => $orderNumber,
                    'reference_number' => $referenceNumber,
                    'address' => $validated['address'],
                    'event_date' => $event['event_date'],
                    'event_time' => $event['event_time'],
                    'event_menu' => $event['event_menu'],
                    'order_type' => $event['order_type'] ?? null,
                    'guest_count' => $event['guest_count'],
                    'estimated_cost' => $event['cost'],
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);

                $createdOrders[] = $order;
            } catch (\Exception $e) {
                Log::error('Failed to create order: ' . $e->getMessage(), [
                    'event' => $event,
                    'tenant_id' => $tenantId,
                    'customer_id' => $customer->id,
                    'exception' => $e->getTraceAsString(),
                ]);
                
                // Show actual error in development, generic message in production
                $errorMessage = config('app.debug') 
                    ? 'Failed to create order: ' . $e->getMessage() . '. Please check if migrations have been run.'
                    : 'Failed to create order. Please try again. If the problem persists, check if database migrations have been run.';
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => $errorMessage]);
            }
        }

        $eventCount = count($createdOrders);
        $message = $eventCount === 1 
            ? "Order created successfully with reference number: {$referenceNumber}!"
            : "{$eventCount} orders created successfully with reference number: {$referenceNumber}!";

        return redirect()->route('orders.index')->with('success', $message);
    }

    public function show(Order $order)
    {
        $order->load('customer', 'invoice.payments');
        
        // If order has reference_number, load all orders with same reference_number
        if (!empty($order->reference_number)) {
            $relatedOrders = Order::with('customer')
                ->where('tenant_id', auth()->user()->tenant_id)
                ->where('reference_number', $order->reference_number)
                ->orderBy('event_date', 'asc')
                ->orderBy('event_time', 'asc')
                ->get();
        } else {
            // Single order without reference_number
            $relatedOrders = collect([$order]);
        }
        
        return view('orders.show', compact('order', 'relatedOrders'));
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

    public function edit(Order $order)
    {
        $order->load('customer');
        
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'event_date' => 'required|date',
            'event_time' => 'required|in:morning,afternoon,evening,night_snack',
            'address' => 'required|string',
            'order_type' => 'nullable|string|max:255',
            'guest_count' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'payment_status' => 'required|in:pending,partial,paid',
        ]);

        $tenantId = auth()->user()->tenant_id;

        // Update or create customer
        $customer = Customer::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'mobile' => $validated['customer_mobile'],
            ],
            [
                'name' => $validated['customer_name'],
            ]
        );

        if ($customer->name !== $validated['customer_name']) {
            $customer->update(['name' => $validated['customer_name']]);
        }

        $order->update([
            'customer_id' => $customer->id,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'address' => $validated['address'],
            'order_type' => $validated['order_type'],
            'guest_count' => $validated['guest_count'],
            'estimated_cost' => $validated['estimated_cost'],
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    public function calendar()
    {
        $orders = Order::where('tenant_id', auth()->user()->tenant_id)
            ->with('customer')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'title' => $order->customer->name . ' - ' . $order->order_number,
                    'start' => $order->event_date->format('Y-m-d'),
                    'url' => route('orders.show', $order),
                ];
            });

        return view('orders.calendar', compact('orders'));
    }
}
