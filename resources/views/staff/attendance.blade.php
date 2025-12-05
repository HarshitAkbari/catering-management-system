@extends('layouts.app')

@section('title', 'Staff Attendance')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Staff Attendance</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('staff.attendance.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff</label><select name="staff_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><option value="">Select Staff</option>@foreach($staff as $member)<option value="{{ $member->id }}" {{ $selectedStaff == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label><input type="date" name="date" required value="{{ $selectedDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label><select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><option value="present" {{ $attendance && $attendance->status === 'present' ? 'selected' : '' }}>Present</option><option value="absent" {{ $attendance && $attendance->status === 'absent' ? 'selected' : '' }}>Absent</option></select></div>
                <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Record</button></div>
            </div>
            <div class="mt-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label><textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $attendance->notes ?? '' }}</textarea></div>
        </form>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Attendance</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Staff</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentAttendance as $record)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $record->staff->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $record->date->format('M d, Y') }}</td><td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($record->status) }}</span></td></tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No attendance records</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $recentAttendance->links() }}</div>
    </div>
</div>
@endsection

