@extends('layouts.app')

@section('title', 'Equipment Maintenance')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment Maintenance</h1>
        <a href="{{ route('equipment.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back to Equipment</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Last Maintenance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Next Maintenance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($equipment as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($item->status) }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->last_maintenance_date?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm {{ $item->isMaintenanceDue() ? 'text-red-600 font-semibold' : 'text-gray-900 dark:text-white' }}">{{ $item->next_maintenance_date?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium"><a href="{{ route('equipment.edit', $item) }}" class="text-blue-600 hover:text-blue-900">Update</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No equipment needs maintenance</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

