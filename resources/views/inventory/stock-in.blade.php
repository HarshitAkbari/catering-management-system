@extends('layouts.app')

@section('title', 'Stock In')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Stock In</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('inventory.stock-in.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="inventory_item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Inventory Item</label>
                    <select name="inventory_item_id" id="inventory_item_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Item</option>
                        @foreach($inventoryItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                    @error('inventory_item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" required step="0.01" min="0.01" value="{{ old('quantity') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price (Optional)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vendor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vendor (Optional)</label>
                    <select name="vendor_id" id="vendor_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Add Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection

