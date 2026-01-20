@extends('layouts.app')

@section('title', $page_title ?? 'Order Types')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title">{{ $page_title ?? 'Order Types' }} List</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="card-subtitle text-muted">
                                Manage and organize order types for your catering orders
                            </h6>
                        </div>
                    </div>
                    <a href="{{ route('settings.order-types.create') }}" class="btn btn-primary btn-add">Add {{ $page_title ?? 'Order Type' }}</a>
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
                                        <td colspan="4" class="text-center">No order types found. <a href="{{ route('settings.order-types.create') }}">Create one</a></td>
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

