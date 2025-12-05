@extends('layouts.app')

@section('title', 'Company Profile')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Company Profile</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('settings.company-profile.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label><input type="text" name="name" required value="{{ old('name', $tenant->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label><input type="email" name="email" required value="{{ old('email', $tenant->email) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label><input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo URL</label><input type="url" name="logo_url" value="{{ old('logo_url', $tenant->logo_url) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('logo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label><textarea name="address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address', $tenant->address) }}</textarea>@error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('settings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection

