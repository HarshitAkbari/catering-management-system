@extends('layouts.app')

@section('title', $page_title ?? 'Staff')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Staff' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('staff.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end mb-3">
                        <!-- Name Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="name_filter" class="form-label">Name</label>
                            <input type="text" name="name_like" id="name_filter" value="{{ $filterValues['name_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by name">
                        </div>

                        <!-- Phone Filter -->
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <label for="phone_filter" class="form-label">Phone</label>
                            <input type="text" name="phone_like" id="phone_filter" value="{{ $filterValues['phone_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by phone">
                        </div>

                        <!-- Email Filter -->
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <label for="email_filter" class="form-label">Email</label>
                            <input type="text" name="email_like" id="email_filter" value="{{ $filterValues['email_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by email">
                        </div>

                        <!-- Role Filter -->
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <label for="role_filter" class="form-label">Role</label>
                            <select name="staff_role" id="role_filter" class="form-control form-control-sm">
                                <option value="">All Roles</option>
                                @foreach($roles ?? [] as $role)
                                    <option value="{{ $role }}" {{ ($filterValues['staff_role'] ?? '') == $role ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                            <label for="status_filter" class="form-label">Status</label>
                            <select name="status" id="status_filter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ ($filterValues['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($filterValues['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <x-filter-buttons resetRoute="{{ route('staff.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="datatable table table-sm mb-0 table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Total Events</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staff as $member)
                                <tr class="btn-reveal-trigger">
                                    <td class="py-3">
                                        <a href="{{ route('staff.show', $member) }}">
                                            <div class="media d-flex align-items-center">
                                                <div class="avatar avatar-xl me-2">
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
                                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="media-body">
                                                    <h5 class="mb-0 fs--1">{{ $member->name }}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="py-2">
                                        <a href="tel:{{ $member->phone }}">{{ $member->phone }}</a>
                                    </td>
                                    <td class="py-2">
                                        @if($member->email)
                                            <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <span class="badge badge-info light">{{ $member->staff_role }}</span>
                                    </td>
                                    <td class="py-2">
                                        @if($member->status === 'active')
                                            <span class="badge badge-success light">Active</span>
                                        @else
                                            <span class="badge badge-secondary light">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-2">
                                        <span class="badge badge-primary light">
                                            {{ $member->orders_count }} {{ Str::plural('event', $member->orders_count) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-end">
                                        <a href="{{ route('staff.show', $member) }}" class="btn btn-primary btn-xs" title="View">
                                            View
                                        </a>
                                        @hasPermission('staff.edit')
                                        <a href="{{ route('staff.edit', $member) }}" class="btn btn-info btn-xs" title="Edit">
                                            Edit
                                        </a>
                                        @if($member->status === 'active')
                                            <button type="button" 
                                                class="btn btn-danger btn-xs" 
                                                onclick="showSettingsDeactivationModal('staff-deactivation-modal', {{ json_encode($member->name) }}, 'staff member', {{ json_encode(route('staff.toggle', $member)) }}, 'PATCH')">
                                                Deactivate
                                            </button>
                                        @else
                                            <button type="button" 
                                                class="btn btn-success btn-xs" 
                                                onclick="showSettingsActivationModal('staff-activation-modal', {{ json_encode($member->name) }}, 'staff member', {{ json_encode(route('staff.toggle', $member)) }}, 'PATCH')">
                                                Activate
                                            </button>
                                        @endif
                                        @endhasPermission
                                        @hasPermission('staff.delete')
                                        <button type="button" 
                                            class="btn btn-danger btn-xs" 
                                            onclick="showDeleteModal('staff-delete-modal', {{ json_encode($member->name) }}, {{ json_encode(route('staff.destroy', $member)) }})"
                                            title="Delete">
                                            Delete
                                        </button>
                                        @endhasPermission
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <p class="text-muted mb-1">No staff members found</p>
                                            <p class="text-muted small">Add your first staff member to get started</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<x-delete-modal 
    id="staff-delete-modal"
    title="Confirm Deletion"
/>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="staff-deactivation-modal"
    setting-type="staff member"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="staff-activation-modal"
    setting-type="staff member"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

