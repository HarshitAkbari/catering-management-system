@extends('layouts.app')

@section('title', 'Assign Staff')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Staff to Event</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Order: <span class="font-semibold">{{ $order->order_number }}</span></p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Event Date: <span class="font-semibold">{{ $order->event_date->format('M d, Y') }}</span></p>
        </div>
        <form action="{{ route('staff.assign.store', $order) }}" method="POST">
            @csrf
            <div id="staff-container" class="space-y-4">
                @foreach($assignedStaff as $index => $member)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 staff-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff Member</label>
                            <select name="staff_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Staff</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $assignedStaff->contains($member->id) ? 'selected' : '' }}>{{ $member->name }} ({{ ucfirst($member->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role for this Event</label>
                            <input type="text" name="roles[]" value="{{ $member->pivot->role ?? '' }}" placeholder="e.g., Head Cook" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                @endforeach
                @if($assignedStaff->isEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 staff-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff Member</label>
                            <select name="staff_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Staff</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->role) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role for this Event</label>
                            <input type="text" name="roles[]" placeholder="e.g., Head Cook" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <button type="button" onclick="addStaffRow()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Add Another Staff</button>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Assign Staff</button>
            </div>
        </form>
    </div>
</div>
<script>
function addStaffRow() {
    const container = document.getElementById('staff-container');
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 staff-item';
    newRow.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff Member</label>
            <select name="staff_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Select Staff</option>
                @foreach($staff as $member)
                    <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->role) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role for this Event</label>
            <input type="text" name="roles[]" placeholder="e.g., Head Cook" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
    `;
    container.appendChild(newRow);
}
</script>
@endsection

