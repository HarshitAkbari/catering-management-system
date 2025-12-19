@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Equipment</h1>
        <div class="flex space-x-3">
            <a href="{{ route('equipment.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Add Equipment</a>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($equipment as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->category ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $item->available_quantity }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($item->status) }}</span></td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('equipment.show', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('equipment.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('equipment.destroy', $item) }}" method="POST" class="inline">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No equipment found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $equipment->links() }}</div>
    </div>
</div>
@endsection

