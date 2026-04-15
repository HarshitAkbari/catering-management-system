@extends('layouts.app')

@section('title', $page_title ?? 'Event Menu Items')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Event Menu Items' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('orders.event-menu-items.create') }}" class="btn btn-sm btn-primary btn-add">Add {{ $page_title ?? 'Event Menu Item' }}</a>
                </div>
                <div class="card-body">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('orders.event-menu-items') }}" class="mb-4">
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
                        <x-filter-buttons resetRoute="{{ route('orders.event-menu-items') }}" />
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>
                                        <x-table.sort-link field="created_by" label="Created By" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="created_at" label="Created At" />
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($eventMenuItems as $eventMenuItem)
                                    <tr data-status-id="{{ $eventMenuItem->id }}">
                                        <td>{{ $eventMenuItem->id }}</td>
                                        <td>{{ $eventMenuItem->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $eventMenuItem->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $eventMenuItem->id }}">
                                                {{ $eventMenuItem->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $eventMenuItem->created_by ? ($eventMenuItem->creator->name ?? 'N/A') : 'System Created' }}
                                        </td>
                                        <td>
                                            {{ $eventMenuItem->created_at ? $eventMenuItem->created_at->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.event-menu-items.edit', $eventMenuItem) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($eventMenuItem->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('event-menu-item-deactivation-modal', '{{ $eventMenuItem->name }}', 'event menu item', '{{ route('orders.event-menu-items.toggle', $eventMenuItem) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('event-menu-item-activation-modal', '{{ $eventMenuItem->name }}', 'event menu item', '{{ route('orders.event-menu-items.toggle', $eventMenuItem) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                            <button type="button" 
                                                class="btn btn-danger btn-xs" 
                                                onclick="showDeleteModal('event-menu-item-delete-modal', '{{ $eventMenuItem->name }}', '{{ route('orders.event-menu-items.destroy', $eventMenuItem) }}')">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No event menu items found</p>
                                                <p class="text-muted small">Create a new event menu item to get started</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $eventMenuItems->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<x-delete-modal 
    id="event-menu-item-delete-modal"
    title="Delete Event Menu Item"
    delete-button-text="Delete Event Menu Item"
/>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="event-menu-item-deactivation-modal"
    setting-type="event menu item"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="event-menu-item-activation-modal"
    setting-type="event menu item"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection


