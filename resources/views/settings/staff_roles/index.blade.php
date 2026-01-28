@extends('layouts.app')

@section('title', $page_title ?? 'Staff Roles')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Staff Roles' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('settings.staff-roles.create') }}" class="btn btn-sm btn-primary btn-add">Add {{ $page_title ?? 'Staff Role' }}</a>
                </div>
                <div class="card-body">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('settings.staff-roles') }}" class="mb-4">
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
                        <x-filter-buttons resetRoute="{{ route('settings.staff-roles') }}" />
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffRoles as $role)
                                    <tr data-status-id="{{ $role->id }}">
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->description ? \Illuminate\Support\Str::limit($role->description, 50) : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $role->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $role->id }}">
                                                {{ $role->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.staff-roles.edit', $role) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($role->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('staff-role-deactivation-modal', '{{ $role->name }}', 'staff role', '{{ route('settings.staff-roles.toggle', $role) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('staff-role-activation-modal', '{{ $role->name }}', 'staff role', '{{ route('settings.staff-roles.toggle', $role) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                            <button type="button" 
                                                class="btn btn-danger btn-xs" 
                                                onclick="if(confirm('Are you sure you want to delete this staff role?')) { document.getElementById('delete-form-{{ $role->id }}').submit(); }">
                                                Delete
                                            </button>
                                            <form id="delete-form-{{ $role->id }}" action="{{ route('settings.staff-roles.destroy', $role) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No staff roles found</p>
                                                <p class="text-muted small">Create a new staff role to get started</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $staffRoles->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="staff-role-deactivation-modal"
    setting-type="staff role"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="staff-role-activation-modal"
    setting-type="staff role"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

