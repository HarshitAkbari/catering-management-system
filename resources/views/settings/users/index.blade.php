@extends('layouts.app')

@section('title', $page_title ?? 'Users')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Users' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary btn-add">Add {{ $page_title ?? 'User' }}</a>
                </div>
                <div class="card-body">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <!-- Preserve sort parameters -->
                        @if(request('sort_by'))
                            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if(request('sort_order'))
                            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                        @endif
                        
                        <div class="row g-2 align-items-end mb-3">
                            <!-- Name Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="name_filter" class="form-label">Name</label>
                                <input type="text" name="name_like" id="name_filter" value="{{ $filterValues['name_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by name">
                            </div>

                            <!-- Email Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="email_filter" class="form-label">Email</label>
                                <input type="text" name="email_like" id="email_filter" value="{{ $filterValues['email_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by email">
                            </div>

                            <!-- Role Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="role_filter" class="form-label">Role</label>
                                <select name="role" id="role_filter" class="form-control form-control-sm">
                                    <option value="">All Roles</option>
                                    <option value="admin" {{ ($filterValues['role'] ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ ($filterValues['role'] ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ ($filterValues['role'] ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="status_filter" class="form-label">Status</label>
                                <select name="status" id="status_filter" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="active" {{ ($filterValues['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ ($filterValues['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <x-filter-buttons resetRoute="{{ route('users.index') }}" />
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <x-table.sort-link field="name" label="Name" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="email" label="Email" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="role" label="Role" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="status" label="Status" />
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="py-3">
                                            <div class="media d-flex align-items-center">
                                                <div class="avatar avatar-xl me-2">
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="mb-0 fs--1">{{ $user->name }}</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2">
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                        </td>
                                        <td class="py-2">
                                            <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'manager' ? 'warning' : 'info') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                                                {{ $user->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            @hasPermission('users.edit')
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @endhasPermission
                                            @if($user->id !== auth()->id())
                                                @if($user->is_active)
                                                    <button type="button" 
                                                        class="btn btn-danger btn-xs" 
                                                        onclick="showSettingsDeactivationModal('user-deactivation-modal', '{{ $user->name }}', 'user', '{{ route('users.toggle', $user) }}', 'PATCH')">
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                        class="btn btn-success btn-xs" 
                                                        onclick="showSettingsActivationModal('user-activation-modal', '{{ $user->name }}', 'user', '{{ route('users.toggle', $user) }}', 'PATCH')">
                                                        Activate
                                                    </button>
                                                @endif
                                            @endif
                                            @hasPermission('users.delete')
                                            @if($user->id !== auth()->id())
                                            <x-delete-button 
                                                item-name="{{ $user->name }}"
                                                delete-url="{{ route('users.destroy', $user) }}"
                                            />
                                            @endif
                                            @endhasPermission
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No users found</p>
                                                <p class="text-muted small">Create a new user to get started</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-delete-modal id="deleteModal" />

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="user-deactivation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="user-activation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

