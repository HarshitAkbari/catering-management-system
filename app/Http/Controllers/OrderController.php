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

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Build filters from request
        $filters = [];
        
        // Status filter
        if ($request->has('status') && is_array($request->status) && !empty($request->status)) {
            $filters['status'] = $request->status;
        }
        
        // Payment status filter
        if ($request->has('payment_status') && is_array($request->payment_status) && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }
        
        // Event time filter
        if ($request->has('event_time') && is_array($request->event_time) && !empty($request->event_time)) {
            $filters['event_time'] = $request->event_time;
        }
        
        // Order type filter
        if ($request->has('order_type') && is_array($request->order_type) && !empty($request->order_type)) {
            $filters['order_type'] = $request->order_type;
        }
        
        // Event date range filter
        if ($request->has('event_date_between') && is_array($request->event_date_between)) {
            $dateRange = $request->event_date_between;
            if (isset($dateRange['from']) && isset($dateRange['to']) && 
                !empty($dateRange['from']) && !empty($dateRange['to'])) {
                $filters['event_date_between'] = [
                    'from' => $dateRange['from'],
                    'to' => $dateRange['to'],
                ];
            }
        }
        
        // Customer search filter
        if ($request->has('customer_search') && !empty($request->customer_search)) {
            $filters['customer'] = [
                '_or_where' => [
                    [
                        'type' => 'relation',
                        'relation' => 'customer',
                        'search_term' => $request->customer_search,
                        'filters' => ['name', 'mobile', 'email'],
                    ],
                ],
            ];
        }
        
        // Sorting parameters
        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $filters['sort_by'] = $request->sort_by;
        }
        if ($request->has('sort_order') && !empty($request->sort_order)) {
            $filters['sort_order'] = $request->sort_order;
        }
        
        $orders = $this->orderService->getGroupedOrders($tenantId, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'status' => $request->input('status', []),
            'payment_status' => $request->input('payment_status', []),
            'event_time' => $request->input('event_time', []),
            'order_type' => $request->input('order_type', []),
            'event_date_between' => $request->input('event_date_between', []),
            'customer_search' => $request->input('customer_search', ''),
        ];

        $page_title = 'Orders';
        $subtitle = 'Manage and track all your catering orders';

        return view('orders.index', compact('orders', 'filterValues', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $page_title = 'Create New Order';
        return view('orders.create', compact('page_title'));
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
            ? "Order created successfully !"
            : "Orders created successfully !";

        return redirect()->route('orders.index')->with('success', $message);
    }

    public function show(Order $order, Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $order->load('customer', 'invoice.payments');
        
        // Build filters from request
        $filters = [];
        
        // Status filter
        if ($request->has('status') && is_array($request->status) && !empty($request->status)) {
            $filters['status'] = $request->status;
        }
        
        // Payment status filter
        if ($request->has('payment_status') && is_array($request->payment_status) && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }
        
        // Event time filter
        if ($request->has('event_time') && is_array($request->event_time) && !empty($request->event_time)) {
            $filters['event_time'] = $request->event_time;
        }
        
        // Order type filter
        if ($request->has('order_type') && is_array($request->order_type) && !empty($request->order_type)) {
            $filters['order_type'] = $request->order_type;
        }
        
        // Event date range filter
        if ($request->has('event_date_between') && is_array($request->event_date_between)) {
            $dateRange = $request->event_date_between;
            if (isset($dateRange['from']) && isset($dateRange['to']) && 
                !empty($dateRange['from']) && !empty($dateRange['to'])) {
                $filters['event_date_between'] = [
                    'from' => $dateRange['from'],
                    'to' => $dateRange['to'],
                ];
            }
        }
        
        // Load all orders with same order_number, with filters applied
        $relatedOrders = $this->orderService->getByOrderNumber($order->order_number, $tenantId, $filters);
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'status' => $request->input('status', []),
            'payment_status' => $request->input('payment_status', []),
            'event_time' => $request->input('event_time', []),
            'order_type' => $request->input('order_type', []),
            'event_date_between' => $request->input('event_date_between', []),
        ];
        
        $page_title = 'Order Details';
        return view('orders.show', compact('order', 'relatedOrders', 'filterValues', 'page_title'));
    }

    public function edit(Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $order->load('customer');
        
        // Load all orders with same order_number
        $relatedOrders = $this->orderService->getByOrderNumber($order->order_number, $tenantId);
        
        $page_title = 'Edit Order';
        return view('orders.edit', compact('order', 'relatedOrders', 'page_title'));
    }

    public function update(Request $request, Order $order)
    {
        $tenantId = auth()->user()->tenant_id;
        
        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

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

        $result = $this->orderService->updateOrders(
            $order->order_number,
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
        $message = $eventCount === 1 
            ? "Order updated successfully!"
            : "Orders updated successfully!";

        return redirect()->route('orders.index')->with('success', $message);
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

    public function updateGroupStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $tenantId = auth()->user()->tenant_id;

        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $result = $this->orderService->updateGroupStatus(
            $order->order_number,
            $validated['status'],
            $tenantId
        );

        if (!$result['status']) {
            return redirect()->route('orders.show', $order)
                ->with('error', $result['message']);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', $result['message']);
    }
}
