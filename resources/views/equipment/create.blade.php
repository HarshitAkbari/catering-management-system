@extends('layouts.app')

@section('title', 'Add Equipment')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Equipment</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('equipment.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label><input type="text" name="name" required value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label><input type="text" name="category" value="{{ old('category') }}" placeholder="e.g., Tables, Chairs" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Quantity</label><input type="number" name="quantity" required min="0" value="{{ old('quantity') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Quantity</label><input type="number" name="available_quantity" required min="0" value="{{ old('available_quantity') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('available_quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label><select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"><option value="available">Available</option><option value="damaged">Damaged</option></select>@error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('equipment.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Equipment</button>
            </div>
        </form>
    </div>
</div>
@endsection

