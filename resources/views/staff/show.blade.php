@extends('layouts.app')

@section('title', 'Staff Details')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $staff->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('staff.edit', $staff) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">Edit</a>
            <a href="{{ route('staff.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back</a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Staff Information</h2>
            <dl class="space-y-3">
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $staff->name }}</dd></div>
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($staff->role) }}</dd></div>
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $staff->phone }}</dd></div>
                @if($staff->email)<div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $staff->email }}</dd></div>@endif
                @if($staff->address)<div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt><dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $staff->address }}</dd></div>@endif
                <div><dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt><dd class="mt-1"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $staff->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($staff->status) }}</span></dd></div>
            </dl>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Assignments</h2>
            <div class="space-y-3">
                @forelse($staff->orders->take(10) as $order)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->event_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No event assignments yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

