@extends('layouts.app')

@section('title', 'Equipment Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $equipment->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('equipment.edit', $equipment) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">Edit</a>
            <a href="{{ route('equipment.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back</a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Equipment Information</h2>
            <dl class="space-y-3">
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->name }}</dd></div>
                @if($equipment->category)<div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->category }}</dd></div>@endif
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Quantity</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->quantity }}</dd></div>
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Available Quantity</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->available_quantity }}</dd></div>
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt><dd class="mt-1"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $equipment->status === 'available' ? 'bg-green-100 text-green-800' : ($equipment->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($equipment->status) }}</span></dd></div>
                @if($equipment->last_maintenance_date)<div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Maintenance</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $equipment->last_maintenance_date->format('M d, Y') }}</dd></div>@endif
                @if($equipment->next_maintenance_date)<div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Next Maintenance</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white {{ $equipment->isMaintenanceDue() ? 'text-red-600 font-semibold' : '' }}">{{ $equipment->next_maintenance_date->format('M d, Y') }}</dd></div>@endif
            </dl>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Assignments</h2>
            <div class="space-y-3">
                @forelse($equipment->orders->take(10) as $order)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->pivot->quantity }} units</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->event_date->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No event assignments yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

