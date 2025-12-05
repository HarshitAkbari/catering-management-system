@extends('layouts.app')

@section('title', 'Inventory Item Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $inventoryItem->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('inventory.edit', $inventoryItem) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">Edit</a>
            <a href="{{ route('inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Item Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryItem->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryItem->unit }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Stock</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ number_format($inventoryItem->current_stock, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minimum Stock</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ number_format($inventoryItem->minimum_stock, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price Per Unit</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">₹{{ number_format($inventoryItem->price_per_unit, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        @if($inventoryItem->isLowStock())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Low Stock</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">In Stock</span>
                        @endif
                    </dd>
                </div>
                @if($inventoryItem->description)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $inventoryItem->description }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Transactions</h2>
            <div class="space-y-3">
                @forelse($inventoryItem->stockTransactions->take(10) as $transaction)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium {{ $transaction->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ strtoupper($transaction->type) }}
                            </span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ number_format($transaction->quantity, 2) }} {{ $inventoryItem->unit }}</span>
                        </div>
                        @if($transaction->vendor)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Vendor: {{ $transaction->vendor->name }}</p>
                        @endif
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No transactions yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

