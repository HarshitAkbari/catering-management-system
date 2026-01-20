@extends('layouts.app')

@section('title', $page_title ?? 'Inventory Units')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title">{{ $page_title ?? 'Inventory Units' }} List</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="card-subtitle text-muted">
                                Manage and organize inventory units for your catering inventory
                            </h6>
                        </div>
                    </div>
                    <a href="{{ route('settings.inventory-units.create') }}" class="btn btn-primary btn-add">Add {{ $page_title ?? 'Inventory Unit' }}</a>
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
                                @forelse($inventoryUnits as $unit)
                                    <tr data-status-id="{{ $unit->id }}">
                                        <td>{{ $unit->id }}</td>
                                        <td>{{ $unit->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $unit->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $unit->id }}">
                                                {{ $unit->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.inventory-units.edit', $unit) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($unit->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('inventory-unit-deactivation-modal', '{{ $unit->name }}', 'inventory unit', '{{ route('settings.inventory-units.toggle', $unit) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('inventory-unit-activation-modal', '{{ $unit->name }}', 'inventory unit', '{{ route('settings.inventory-units.toggle', $unit) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No inventory units found. <a href="{{ route('settings.inventory-units.create') }}">Create one</a></td>
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
    modal-id="inventory-unit-deactivation-modal"
    setting-type="inventory unit"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="inventory-unit-activation-modal"
    setting-type="inventory unit"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

