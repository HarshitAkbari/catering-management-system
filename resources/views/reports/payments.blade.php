@extends('layouts.app')

@section('title', 'Payments Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payments Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
        </form>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Payments</p><p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_payments'] }}</p></div>
            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['total_amount'], 2) }}</p></div>
            <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Cash</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['cash'], 2) }}</p></div>
            <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">UPI</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['upi'], 2) }}</p></div>
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4"><p class="text-sm text-gray-600 dark:text-gray-400">Bank Transfer</p><p class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($summary['bank_transfer'], 2) }}</p></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Order</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mode</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payments as $payment)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $payment->payment_date->format('M d, Y') }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $payment->invoice->order->order_number ?? '-' }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">₹{{ number_format($payment->amount, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $payment->payment_mode)) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No payments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

