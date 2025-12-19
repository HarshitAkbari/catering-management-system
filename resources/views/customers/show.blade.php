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
        @if($groupedOrdersList->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Order #</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Event Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Payment Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($groupedOrdersList as $group)
                            @php
                                $firstOrder = $group['orders']->first();
                                $status = $group['status'];
                                $paymentStatus = $group['payment_status'];
                                $orderNumber = $group['order_number'];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    {{ $orderNumber }}
                                    @if($group['orders']->count() > 1)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $group['orders']->count() }} orders)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    {{ $group['event_date'] ? $group['event_date']->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    ₹{{ number_format($group['total_amount'], 2) }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $status === 'confirmed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                        {{ $status === 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                        {{ $status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                        {{ $status === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $paymentStatus === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                        {{ $paymentStatus === 'partial' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                        {{ $paymentStatus === 'pending' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                        {{ $paymentStatus === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                        {{ ucfirst($paymentStatus) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <a href="{{ route('orders.show', $firstOrder) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
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

