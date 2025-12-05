@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('settings.company-profile') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Company Profile</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage company information and branding</p>
        </a>
        <a href="{{ route('settings.invoice-branding') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Invoice Branding</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Configure invoice templates and branding</p>
        </a>
        <a href="{{ route('settings.event-types') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Event Types</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Manage event categories</p>
        </a>
        <a href="{{ route('settings.notifications') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notifications</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Configure notification preferences</p>
        </a>
    </div>
</div>
@endsection

