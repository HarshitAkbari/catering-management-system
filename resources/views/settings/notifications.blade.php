@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Settings</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('settings.notifications.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="flex items-center justify-between"><label class="text-sm font-medium text-gray-700 dark:text-gray-300">SMS Notifications</label><input type="checkbox" name="sms_enabled" value="1" {{ ($settings['sms_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300"></div>
                <div class="flex items-center justify-between"><label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notifications</label><input type="checkbox" name="email_enabled" value="1" {{ ($settings['email_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300"></div>
                <div class="flex items-center justify-between"><label class="text-sm font-medium text-gray-700 dark:text-gray-300">Low Stock Alerts</label><input type="checkbox" name="low_stock_alert" value="1" {{ ($settings['low_stock_alert'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300"></div>
                <div class="flex items-center justify-between"><label class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Reminders</label><input type="checkbox" name="payment_reminder" value="1" {{ ($settings['payment_reminder'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300"></div>
                <div class="flex items-center justify-between"><label class="text-sm font-medium text-gray-700 dark:text-gray-300">Maintenance Reminders</label><input type="checkbox" name="maintenance_reminder" value="1" {{ ($settings['maintenance_reminder'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300"></div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('settings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection

