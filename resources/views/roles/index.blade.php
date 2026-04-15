@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles & Permissions</h1>
        <button onclick="document.getElementById('role-form').classList.toggle('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Add Role</button>
    </div>
    <div id="role-form" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role Name</label><input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Display Name</label><input type="text" name="display_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></div>
            </div>
            <div class="mt-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label><div class="grid grid-cols-2 md:grid-cols-4 gap-2">@foreach($permissions as $permission)<label class="flex items-center"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rounded border-gray-300 mr-2"><span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->display_name ?? $permission->name }}</span></label>@endforeach</div></div>
            <div class="mt-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label><textarea name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea></div>
            <div class="mt-4"><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Role</button></div>
        </form>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="datatable min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Role</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Display Name</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Permissions</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th></tr></thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($roles as $role)
                        <tr><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $role->name }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $role->display_name ?? '-' }}</td><td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $role->permissions->count() }} permissions</td><td class="px-6 py-4 text-sm font-medium"><a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a><x-delete-button item-name="{{ $role->display_name ?? $role->name }}" delete-url="{{ route('roles.destroy', $role) }}" button-class="text-red-600 hover:text-red-900 bg-transparent border-0 p-0" /></td></tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No roles found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<x-delete-modal id="deleteModal" />
@endsection

