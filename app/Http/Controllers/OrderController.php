<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\EventTime;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderType;
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
        
        // Status filter (now using order_status_id)
        if ($request->has('order_status_id') && is_array($request->order_status_id) && !empty($request->order_status_id)) {
            $filters['order_status_id'] = $request->order_status_id;
        }
        
        // Payment status filter
        if ($request->has('payment_status') && is_array($request->payment_status) && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }
        
        // Event time filter (now using event_time_id)
        if ($request->has('event_time_id') && is_array($request->event_time_id) && !empty($request->event_time_id)) {
            $filters['event_time_id'] = $request->event_time_id;
        }
        
        // Order type filter (now using order_type_id)
        if ($request->has('order_type_id') && is_array($request->order_type_id) && !empty($request->order_type_id)) {
            $filters['order_type_id'] = $request->order_type_id;
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
            'order_status_id' => $request->input('order_status_id', []),
            'payment_status' => $request->input('payment_status', []),
            'event_time_id' => $request->input('event_time_id', []),
            'order_type_id' => $request->input('order_type_id', []),
            'event_date_between' => $request->input('event_date_between', []),
            'customer_search' => $request->input('customer_search', ''),
        ];

        $page_title = 'Orders';
        $subtitle = 'Manage and track all your catering orders';

        return view('orders.index', compact('orders', 'filterValues', 'page_title', 'subtitle'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        $eventTimes = EventTime::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })->where('is_active', true)->orderBy('is_system', 'desc')->orderBy('name')->get();
        $orderTypes = OrderType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();
        
        $page_title = 'Create New Order';
        return view('orders.create', compact('page_title', 'eventTimes', 'orderTypes'));
    }

    public function store(StoreOrderRequest $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $validated = $request->validated();

        $result = $this->orderService->createOrders(
            $validated['events'],
            [
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_mobile' => $validated['customer_mobile'],
                'customer_secondary_mobile' => $validated['customer_secondary_mobile'] ?? null,
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

        $order->load('customer', 'invoice.payments', 'eventTime', 'orderType', 'orderStatus', 'staff');
        
        // Build filters from request
        $filters = [];
        
        // Status filter (now using order_status_id)
        if ($request->has('order_status_id') && is_array($request->order_status_id) && !empty($request->order_status_id)) {
            $filters['order_status_id'] = $request->order_status_id;
        }
        
        // Payment status filter
        if ($request->has('payment_status') && is_array($request->payment_status) && !empty($request->payment_status)) {
            $filters['payment_status'] = $request->payment_status;
        }
        
        // Event time filter (now using event_time_id)
        if ($request->has('event_time_id') && is_array($request->event_time_id) && !empty($request->event_time_id)) {
            $filters['event_time_id'] = $request->event_time_id;
        }
        
        // Order type filter (now using order_type_id)
        if ($request->has('order_type_id') && is_array($request->order_type_id) && !empty($request->order_type_id)) {
            $filters['order_type_id'] = $request->order_type_id;
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
        
        // Get settings for filters and display
        $orderStatuses = OrderStatus::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })->where('is_active', true)->orderBy('is_system', 'desc')->orderBy('name')->get();
        $eventTimes = EventTime::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })->where('is_active', true)->orderBy('is_system', 'desc')->orderBy('name')->get();
        $orderTypes = OrderType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();
        
        // Pass filter values to view for form preservation
        $filterValues = [
            'order_status_id' => $request->input('order_status_id', []),
            'payment_status' => $request->input('payment_status', []),
            'event_time_id' => $request->input('event_time_id', []),
            'order_type_id' => $request->input('order_type_id', []),
            'event_date_between' => $request->input('event_date_between', []),
        ];
        
        $page_title = 'Order Details';
        return view('orders.show', compact('order', 'relatedOrders', 'filterValues', 'page_title', 'orderStatuses', 'eventTimes', 'orderTypes'));
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
        
        $eventTimes = EventTime::where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $tenantId);
        })->where('is_active', true)->orderBy('is_system', 'desc')->orderBy('name')->get();
        $orderTypes = OrderType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();
        
        $page_title = 'Edit Order';
        return view('orders.edit', compact('order', 'relatedOrders', 'page_title', 'eventTimes', 'orderTypes'));
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

        $tenantId = auth()->user()->tenant_id;
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_secondary_mobile' => 'nullable|string|max:20',
            'address' => 'required|string',
            'events' => 'required|array|min:1',
            'events.*.event_date' => 'required|date',
            'events.*.event_time_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($tenantId) {
                if (!EventTime::where('id', $value)
                    ->where(function ($q) use ($tenantId) {
                        $q->whereNull('tenant_id')
                          ->orWhere('tenant_id', $tenantId);
                    })
                    ->where('is_active', true)
                    ->exists()) {
                    $fail('The selected event time is invalid.');
                }
            }],
            'events.*.event_menu' => 'required|string|max:255',
            'events.*.guest_count' => 'required|integer|min:1',
            'events.*.order_type_id' => ['nullable', 'integer', function ($attribute, $value, $fail) use ($tenantId) {
                if ($value && !OrderType::where('id', $value)->where('tenant_id', $tenantId)->where('is_active', true)->exists()) {
                    $fail('The selected order type is invalid.');
                }
            }],
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
                'customer_secondary_mobile' => $validated['customer_secondary_mobile'] ?? null,
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
        $tenantId = auth()->user()->tenant_id;

        if ($order->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'order_status_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($tenantId) {
                if (!OrderStatus::where('id', $value)
                    ->where(function ($q) use ($tenantId) {
                        $q->whereNull('tenant_id')
                          ->orWhere('tenant_id', $tenantId);
                    })
                    ->where('is_active', true)
                    ->exists()) {
                    $fail('The selected order status is invalid.');
                }
            }],
        ]);

        $result = $this->orderService->updateGroupStatus(
            $order->order_number,
            (int) $validated['order_status_id'],
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
