@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vendor->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('vendors.edit', $vendor) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">Edit</a>
            <a href="{{ route('vendors.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendor Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vendor->name }}</dd>
                </div>
                @if($vendor->contact_person)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact Person</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vendor->contact_person }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vendor->phone }}</dd>
                </div>
                @if($vendor->email)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vendor->email }}</dd>
                </div>
                @endif
                @if($vendor->address)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $vendor->address }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Transactions</h2>
            <div class="space-y-3">
                @forelse($vendor->stockTransactions->take(10) as $transaction)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->inventoryItem->name }}</span>
                            <span class="text-sm text-green-600">{{ number_format($transaction->quantity, 2) }} {{ $transaction->inventoryItem->unit }}</span>
                        </div>
                        @if($transaction->price)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Price: ₹{{ number_format($transaction->price, 2) }}</p>
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

