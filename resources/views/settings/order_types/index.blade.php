@extends('layouts.app')

@section('title', $page_title ?? 'Order Types')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Order Types' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('settings.order-types.create') }}" class="btn btn-sm btn-primary btn-add">Add {{ $page_title ?? 'Order Type' }}</a>
                </div>
                <div class="card-body">

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('settings.order-types') }}" class="mb-4">
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
                        <x-filter-buttons resetRoute="{{ route('settings.order-types') }}" />
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderTypes as $orderType)
                                    <tr data-status-id="{{ $orderType->id }}">
                                        <td>{{ $orderType->id }}</td>
                                        <td>{{ $orderType->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $orderType->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $orderType->id }}">
                                                {{ $orderType->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.order-types.edit', $orderType) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($orderType->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('order-type-deactivation-modal', '{{ $orderType->name }}', 'order type', '{{ route('settings.order-types.toggle', $orderType) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('order-type-activation-modal', '{{ $orderType->name }}', 'order type', '{{ route('settings.order-types.toggle', $orderType) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No order types found</p>
                                                <p class="text-muted small">Create a new order type to get started</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $orderTypes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="order-type-deactivation-modal"
    setting-type="order type"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="order-type-activation-modal"
    setting-type="order type"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

