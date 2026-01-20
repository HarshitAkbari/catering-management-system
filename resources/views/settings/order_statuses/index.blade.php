@extends('layouts.app')

@section('title', $page_title ?? 'Order Statuses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title">{{ $page_title ?? 'Order Statuses' }} List</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="card-subtitle text-muted">
                                Manage and organize order statuses for your catering orders
                            </h6>
                        </div>
                    </div>
                    <a href="{{ route('settings.order-statuses.create') }}" class="btn btn-primary btn-add">Add {{ $page_title ?? 'Order Status' }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderStatuses as $status)
                                    <tr data-status-id="{{ $status->id }}">
                                        <td>{{ $status->id }}</td>
                                        <td>{{ $status->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $status->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $status->id }}">
                                                {{ $status->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.order-statuses.edit', $status) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($status->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('order-status-deactivation-modal', '{{ $status->name }}', 'order status', '{{ route('settings.order-statuses.toggle', $status) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('order-status-activation-modal', '{{ $status->name }}', 'order status', '{{ route('settings.order-statuses.toggle', $status) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No order statuses found. <a href="{{ route('settings.order-statuses.create') }}">Create one</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="order-status-deactivation-modal"
    setting-type="order status"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="order-status-activation-modal"
    setting-type="order status"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

