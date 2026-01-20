@extends('layouts.app')

@section('title', $page_title ?? 'Event Times')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title">{{ $page_title ?? 'Event Times' }} List</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="card-subtitle text-muted">
                                Manage and organize event times for your catering orders
                            </h6>
                        </div>
                    </div>
                    <a href="{{ route('settings.event-times.create') }}" class="btn btn-primary btn-add">Add {{ $page_title ?? 'Event Time' }}</a>
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
                                @forelse($eventTimes as $eventTime)
                                    <tr data-status-id="{{ $eventTime->id }}">
                                        <td>{{ $eventTime->id }}</td>
                                        <td>{{ $eventTime->name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $eventTime->is_active ? 'success' : 'danger' }} status-badge" data-status-id="{{ $eventTime->id }}">
                                                {{ $eventTime->is_active ? 'Active' : 'In-Active' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('settings.event-times.edit', $eventTime) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            @if($eventTime->is_active)
                                                <button type="button" 
                                                    class="btn btn-danger btn-xs" 
                                                    onclick="showSettingsDeactivationModal('event-time-deactivation-modal', '{{ $eventTime->name }}', 'event time', '{{ route('settings.event-times.toggle', $eventTime) }}', 'PATCH')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="showSettingsActivationModal('event-time-activation-modal', '{{ $eventTime->name }}', 'event time', '{{ route('settings.event-times.toggle', $eventTime) }}', 'PATCH')">
                                                    Activate
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No event times found. <a href="{{ route('settings.event-times.create') }}">Create one</a></td>
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
    modal-id="event-time-deactivation-modal"
    setting-type="event time"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="event-time-activation-modal"
    setting-type="event time"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

