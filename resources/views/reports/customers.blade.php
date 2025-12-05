@extends('layouts.app')

@section('title', 'Customers Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Customers Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Returning Customers ({{ $returningCustomers->count() }})</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($returningCustomers->take(6) as $customer)
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $customer->orders_count }} orders</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total: ₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Phone</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Orders</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total Value</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($customers as $customer)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->mobile }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $customer->orders_count }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">₹{{ number_format($customer->orders_sum_estimated_cost ?? 0, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No customers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

