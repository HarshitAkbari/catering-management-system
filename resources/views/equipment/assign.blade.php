@extends('layouts.app')

@section('title', 'Assign Equipment')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Equipment to Event</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Order: <span class="font-semibold">{{ $order->order_number }}</span></p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Event Date: <span class="font-semibold">{{ $order->event_date->format('M d, Y') }}</span></p>
        </div>
        <form action="{{ route('equipment.assign.store', $order) }}" method="POST">
            @csrf
            <div id="equipment-container" class="space-y-4">
                @foreach($assignedEquipment as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 equipment-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipment</label>
                            <select name="equipment_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Equipment</option>
                                @foreach($equipment as $item)
                                    <option value="{{ $item->id }}" {{ $assignedEquipment->contains($item->id) ? 'selected' : '' }}>{{ $item->name }} (Available: {{ $item->available_quantity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                            <input type="number" name="quantities[]" value="{{ $item->pivot->quantity ?? 1 }}" required min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                @endforeach
                @if($assignedEquipment->isEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 equipment-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipment</label>
                            <select name="equipment_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Equipment</option>
                                @foreach($equipment as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} (Available: {{ $item->available_quantity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                            <input type="number" name="quantities[]" value="1" required min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                @endif
            </div>
            <div class="mt-4">
                <button type="button" onclick="addEquipmentRow()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Add Another Equipment</button>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Assign Equipment</button>
            </div>
        </form>
    </div>
</div>
<script>
function addEquipmentRow() {
    const container = document.getElementById('equipment-container');
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 equipment-item';
    newRow.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Equipment</label>
            <select name="equipment_ids[]" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Select Equipment</option>
                @foreach($equipment as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} (Available: {{ $item->available_quantity }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
            <input type="number" name="quantities[]" value="1" required min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
    `;
    container.appendChild(newRow);
}
</script>
@endsection

