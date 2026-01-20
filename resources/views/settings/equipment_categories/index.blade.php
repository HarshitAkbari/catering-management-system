@extends('layouts.app')

@section('title', $page_title ?? 'Equipment Categories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title">{{ $page_title ?? 'Equipment Categories' }} List</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="card-subtitle text-muted">
                                Manage and organize equipment categories for your catering equipment
                            </h6>
                        </div>
                    </div>
                    <a href="{{ route('settings.equipment-categories.create') }}" class="btn btn-primary btn-add">Add {{ $page_title ?? 'Equipment Category' }}</a>
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
                                @forelse($equipmentCategories as $category)
                                    <tr data-status-id="{{ $category->id }}">
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $category->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $category->id }}">
                                                {{ $category->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.equipment-categories.edit', $category) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($category->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('equipment-category-deactivation-modal', '{{ $category->name }}', 'equipment category', '{{ route('settings.equipment-categories.toggle', $category) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('equipment-category-activation-modal', '{{ $category->name }}', 'equipment category', '{{ route('settings.equipment-categories.toggle', $category) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No equipment categories found. <a href="{{ route('settings.equipment-categories.create') }}">Create one</a></td>
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
    modal-id="equipment-category-deactivation-modal"
    setting-type="equipment category"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="equipment-category-activation-modal"
    setting-type="equipment category"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

