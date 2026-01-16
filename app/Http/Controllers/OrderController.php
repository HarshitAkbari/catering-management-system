<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $orders = $this->orderService->getGroupedOrders($tenantId);

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

        $result = $this->orderService->createOrders(
            $validated['events'],
            [
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_mobile' => $validated['customer_mobile'],
            ],
            $validated['address'],
            $tenantId
        );

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        $eventCount = $result['count'];
        $orderNumber = $result['order_number'];
        $message = $eventCount === 1 
            ? "Order created successfully with order number: {$orderNumber}!"
            : "{$eventCount} orders created successfully with order number: {$orderNumber}!";

        return redirect()->route('orders.index')->with('success', $message);
    }

    public function show(Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $order->load('customer', 'invoice.payments');
        
        // Load all orders with same order_number
        $relatedOrders = $this->orderService->getByOrderNumber($order->order_number, $tenantId);
        
        return view('orders.show', compact('order', 'relatedOrders'));
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

        $result = $this->orderService->updateOrder($order, array_merge($validated, [
            'customer_name' => $validated['customer_name'],
            'customer_mobile' => $validated['customer_mobile'],
        ]), $tenantId);

        if (!$result['status']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
    }

    public function destroy(Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $this->orderService->delete($order);
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    public function calendar()
    {
        $tenantId = auth()->user()->tenant_id;
        $orders = $this->orderService->getCalendarOrders($tenantId);

        return view('orders.calendar', compact('orders'));
    }
}
