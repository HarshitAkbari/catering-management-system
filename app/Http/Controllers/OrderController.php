<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer', 'package')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $packages = Package::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->get();

        return view('orders.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'event_date' => 'required|date',
            'event_time' => 'required|in:morning,afternoon,evening,night_snack',
            'address' => 'required|string',
            'order_type' => 'nullable|string|max:255',
            'guest_count' => 'required|integer|min:1',
            'menu_package_id' => 'nullable|exists:packages,id',
            'estimated_cost' => 'required|numeric|min:0',
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
            ]
        );

        // Update customer name if different
        if ($customer->name !== $validated['customer_name']) {
            $customer->update(['name' => $validated['customer_name']]);
        }

        // Generate order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // Create order
        $order = Order::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer->id,
            'order_number' => $orderNumber,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'address' => $validated['address'],
            'order_type' => $validated['order_type'],
            'guest_count' => $validated['guest_count'],
            'menu_package_id' => $validated['menu_package_id'] ?? null,
            'estimated_cost' => $validated['estimated_cost'],
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }

    public function show(Order $order)
    {
        $order->load('customer', 'package', 'invoice.payments');
        
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $packages = Package::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->get();

        $order->load('customer');
        
        return view('orders.edit', compact('order', 'packages'));
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
            'menu_package_id' => 'nullable|exists:packages,id',
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
            'menu_package_id' => $validated['menu_package_id'] ?? null,
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
}
