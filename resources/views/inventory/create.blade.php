@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Inventory Item</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" required value="{{ old('unit') }}" placeholder="kg, liter, piece" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Stock</label>
                    <input type="number" name="current_stock" id="current_stock" required step="0.01" min="0" value="{{ old('current_stock') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('current_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minimum_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Stock</label>
                    <input type="number" name="minimum_stock" id="minimum_stock" required step="0.01" min="0" value="{{ old('minimum_stock') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('minimum_stock')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price Per Unit</label>
                    <input type="number" name="price_per_unit" id="price_per_unit" required step="0.01" min="0" value="{{ old('price_per_unit') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('price_per_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Item</button>
            </div>
        </form>
    </div>
</div>
@endsection

