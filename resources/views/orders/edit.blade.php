@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Order</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name', $order->customer->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="customer_mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                    <input type="text" name="customer_mobile" id="customer_mobile" required value="{{ old('customer_mobile', $order->customer->mobile) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Date</label>
                    <input type="date" name="event_date" id="event_date" required value="{{ old('event_date', $order->event_date->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="event_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Time</label>
                    <select name="event_time" id="event_time" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="morning" {{ old('event_time', $order->event_time) === 'morning' ? 'selected' : '' }}>Morning</option>
                        <option value="afternoon" {{ old('event_time', $order->event_time) === 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        <option value="evening" {{ old('event_time', $order->event_time) === 'evening' ? 'selected' : '' }}>Evening</option>
                        <option value="night_snack" {{ old('event_time', $order->event_time) === 'night_snack' ? 'selected' : '' }}>Night Snack</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="address" id="address" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address', $order->address) }}</textarea>
                </div>

                <div>
                    <label for="order_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Type</label>
                    <input type="text" name="order_type" id="order_type" value="{{ old('order_type', $order->order_type) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="guest_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Guest Count</label>
                    <input type="number" name="guest_count" id="guest_count" required min="1" value="{{ old('guest_count', $order->guest_count) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Estimated Cost</label>
                    <input type="number" name="estimated_cost" id="estimated_cost" required step="0.01" min="0" value="{{ old('estimated_cost', $order->estimated_cost) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $order->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                    <select name="payment_status" id="payment_status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="pending" {{ old('payment_status', $order->payment_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ old('payment_status', $order->payment_status) === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ old('payment_status', $order->payment_status) === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Order</button>
            </div>
        </form>
    </div>
</div>
@endsection

