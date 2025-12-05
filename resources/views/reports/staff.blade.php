@extends('layouts.app')

@section('title', 'Staff Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Staff Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Staff</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Role</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Events</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Attendance</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($staff as $member)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $member->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ ucfirst($member->role) }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $member->orders_count }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $member->attendance->where('status', 'present')->count() }} / {{ $member->attendance->count() }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No staff found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

