@extends('layouts.app')

@section('title', $page_title ?? 'Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Settings' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="order-statuses-tab" data-bs-toggle="tab" data-bs-target="#order-statuses" type="button" role="tab">
                                Order Statuses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="event-times-tab" data-bs-toggle="tab" data-bs-target="#event-times" type="button" role="tab">
                                Event Times
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="order-types-tab" data-bs-toggle="tab" data-bs-target="#order-types" type="button" role="tab">
                                Order Types
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="settingsTabsContent">
                        <!-- Order Statuses Tab -->
                        <div class="tab-pane fade show active" id="order-statuses" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Order Statuses</h5>
                                <a href="{{ route('settings.order-statuses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Add Status
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Color</th>
                                            <th>Display Order</th>
                                            <th>Default</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orderStatuses as $status)
                                            <tr>
                                                <td>{{ $status->name }}</td>
                                                <td>
                                                    @if($status->color)
                                                        <span class="badge" style="background-color: {{ $status->color }};">{{ $status->color }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $status->display_order }}</td>
                                                <td>{{ $status->is_default ? 'Yes' : 'No' }}</td>
                                                <td>{{ $status->is_active ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    <a href="{{ route('settings.order-statuses.edit', $status) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('settings.order-statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this status?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No order statuses found. <a href="{{ route('settings.order-statuses.create') }}">Create one</a></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Event Times Tab -->
                        <div class="tab-pane fade" id="event-times" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Event Times</h5>
                                <a href="{{ route('settings.event-times.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Add Event Time
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Display Order</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($eventTimes as $eventTime)
                                            <tr>
                                                <td>{{ $eventTime->name }}</td>
                                                <td>{{ $eventTime->display_order }}</td>
                                                <td>{{ $eventTime->is_active ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    <a href="{{ route('settings.event-times.edit', $eventTime) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('settings.event-times.destroy', $eventTime) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event time?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
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

                        <!-- Order Types Tab -->
                        <div class="tab-pane fade" id="order-types" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Order Types</h5>
                                <a href="{{ route('settings.order-types.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Add Order Type
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Display Order</th>
                                            <th>Active</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orderTypes as $orderType)
                                            <tr>
                                                <td>{{ $orderType->name }}</td>
                                                <td>{{ $orderType->display_order }}</td>
                                                <td>{{ $orderType->is_active ? 'Yes' : 'No' }}</td>
                                                <td>
                                                    <a href="{{ route('settings.order-types.edit', $orderType) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form action="{{ route('settings.order-types.destroy', $orderType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order type?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
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
    </div>
</div>
@endsection

