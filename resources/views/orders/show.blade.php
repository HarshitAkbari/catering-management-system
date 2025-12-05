@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('orders.edit', $order) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Edit Order</a>
            <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back to Orders</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Order Number</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Customer</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Contact</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->customer->mobile }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Event Date</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->event_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Event Time</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $order->event_time)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Address</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->address }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Guest Count</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->guest_count }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Estimated Cost</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">₹{{ number_format($order->estimated_cost, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $order->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payment Status</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->payment_status === 'pending' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        @if($order->package)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Package Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Package Name</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->package->name }}</p>
                    </div>
                    @if($order->package->description)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Description</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->package->description }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Package Price</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-white">₹{{ number_format($order->package->price, 2) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

