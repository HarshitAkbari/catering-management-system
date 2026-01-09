@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payments</h1>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="datatable min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orders as $group)
                        @php
                            $paymentStatus = $group['payment_status'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold mr-3">
                                        {{ strtoupper(substr($group['customer']->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $group['customer']->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $group['customer']->mobile }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">₹{{ number_format($group['total_amount'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $paymentStatus === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ $paymentStatus === 'partial' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                    {{ $paymentStatus === 'pending' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $paymentStatus === 'mixed' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    @if($group['invoice'])
                                        <a href="{{ route('invoices.show', $group['invoice']) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 font-medium">
                                            View Invoice
                                        </a>
                                        <span class="text-gray-400">|</span>
                                        <a href="{{ route('invoices.download', $group['invoice']) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Download PDF
                                        </a>
                                    @else
                                        <a href="{{ route('invoices.generate', $group['order_number']) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">
                                            Generate Invoice
                                        </a>
                                    @endif
                                    <span class="text-gray-400">|</span>
                                    @if($group['orders']->count() > 1)
                                        <button type="button" onclick="openPaymentModal('{{ $group['order_number'] }}', {{ $group['orders']->count() }}, '{{ $paymentStatus }}')" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Update Payment
                                        </button>
                                    @else
                                        <a href="{{ route('orders.edit', $group['orders']->first()) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            Update Payment
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Update Modal -->
<x-modal id="payment-modal" title="Update Payment Status" size="standard">
    <form id="payment-update-form" action="{{ route('payments.update-group') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Number</label>
                <input type="text" id="modal-order-number" readonly class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                <input type="hidden" name="order_number" id="hidden-order-number">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Orders</label>
                <input type="text" id="modal-order-count" readonly class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
            </div>
            
            <div>
                <label for="modal-payment-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                <select name="payment_status" id="modal-payment-status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="pending">Pending</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" onclick="closeModal('payment-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
            Cancel
        </button>
        <button type="button" onclick="document.getElementById('payment-update-form').submit()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
            Update Payment Status
        </button>
    </x-slot>
</x-modal>

<script>
    function openPaymentModal(orderNumber, orderCount, currentStatus) {
        document.getElementById('modal-order-number').value = orderNumber;
        document.getElementById('hidden-order-number').value = orderNumber;
        document.getElementById('modal-order-count').value = orderCount + ' order(s)';
        document.getElementById('modal-payment-status').value = currentStatus === 'mixed' ? 'pending' : currentStatus;
        openModal('payment-modal');
    }
</script>
@endsection

