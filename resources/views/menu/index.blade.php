@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Menu Management</h1>

    <!-- Categories and Items -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Categories & Items</h2>
        @if($categories->count() > 0)
            @foreach($categories as $category)
                <div class="mb-6">
                    <h3 class="text-md font-medium text-gray-800 dark:text-white mb-2">{{ $category->name }}</h3>
                    @if($category->menuItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Item Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($category->menuItems as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $item->name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">₹{{ number_format($item->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No items in this category</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-gray-500 dark:text-gray-400">No categories found. Categories and menu items can be managed from the database.</p>
        @endif
    </div>

    <!-- Packages -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Packages</h2>
        @if($packages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Package Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Description</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($packages as $package)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $package->name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $package->description ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">₹{{ number_format($package->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">No packages found. Packages can be managed from the database.</p>
        @endif
    </div>
</div>
@endsection

