@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Profit & Loss Report</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            <div class="flex items-end"><button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button></div>
        </form>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-green-50 dark:bg-green-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Revenue</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₹{{ number_format($revenue, 2) }}</p>
            </div>
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Expenses</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">₹{{ number_format($expenses, 2) }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Profit/Loss</p>
                <p class="text-3xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($profit, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

