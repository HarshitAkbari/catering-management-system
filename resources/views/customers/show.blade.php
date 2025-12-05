@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Customer Details</h1>
        <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800">Back to Customers</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $customer->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mobile</p>
                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $customer->mobile }}</p>
            </div>
            @if($customer->email)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $customer->email }}</p>
                </div>
            @endif
            @if($customer->address)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Address</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $customer->address }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order History</h2>
        @if($customer->orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Order #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Event Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($customer->orders as $order)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $order->event_date->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">₹{{ number_format($order->estimated_cost, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ ucfirst($order->status) }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No orders found for this customer</p>
        @endif
    </div>
</div>
@endsection

