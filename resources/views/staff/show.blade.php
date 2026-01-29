@extends('layouts.app')

@section('title', 'Staff Details')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Staff Details</h4>
                <div class="d-flex gap-2">
                    @hasPermission('staff.edit')
                    <a href="{{ route('staff.edit', $staff) }}" class="btn btn-info btn-sm">Edit</a>
                    @endhasPermission
                    <a href="{{ route('staff.index') }}" class="btn btn-dark btn-xs">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td class="text-muted">{{ $staff->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td class="text-muted"><a href="tel:{{ $staff->phone }}">{{ $staff->phone }}</a></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td class="text-muted">@if($staff->email)<a href="mailto:{{ $staff->email }}">{{ $staff->email }}</a>@else N/A @endif</td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td class="text-muted"><span class="badge badge-info light">{{ $staff->staff_role }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td class="text-muted">
                                    @if($staff->status === 'active')
                                        <span class="badge badge-success light">Active</span>
                                    @else
                                        <span class="badge badge-secondary light">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @if($staff->address)
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td class="text-muted">{{ $staff->address }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Statistics</h5>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $staff->getTotalEvents() }}</h3>
                                        <p class="mb-0">Total Events</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $attendanceRate }}%</h3>
                                        <p class="mb-0">Attendance Rate</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $upcomingEvents->count() }}</h3>
                                        <p class="mb-0">Upcoming Events</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $pastEvents->count() }}</h3>
                                        <p class="mb-0">Past Events</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="staffTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                            Upcoming Events ({{ $upcomingEvents->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                            Past Events ({{ $pastEvents->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                            Attendance History
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab">
                            Performance
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="staffTabsContent">
                    <!-- Upcoming Events Tab -->
                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                        @if($upcomingEvents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Event Date</th>
                                            <th>Event Time</th>
                                            <th>Order Number</th>
                                            <th>Customer</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingEvents as $event)
                                            <tr>
                                                <td>{{ $event->event_date->format('M d, Y') }}</td>
                                                <td>{{ $event->eventTime->name ?? '-' }}</td>
                                                <td><a href="{{ route('orders.show', $event) }}">{{ $event->order_number }}</a></td>
                                                <td>{{ $event->customer->name }}</td>
                                                <td><span class="badge badge-info light">{{ $event->pivot->role ?? $staff->staff_role }}</span></td>
                                                <td><span class="badge badge-primary light">{{ $event->orderStatus->name ?? '-' }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No upcoming events assigned.</p>
                        @endif
                    </div>

                    <!-- Past Events Tab -->
                    <div class="tab-pane fade" id="past" role="tabpanel">
                        @if($pastEvents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Event Date</th>
                                            <th>Event Time</th>
                                            <th>Order Number</th>
                                            <th>Customer</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pastEvents as $event)
                                            <tr>
                                                <td>{{ $event->event_date->format('M d, Y') }}</td>
                                                <td>{{ $event->eventTime->name ?? '-' }}</td>
                                                <td><a href="{{ route('orders.show', $event) }}">{{ $event->order_number }}</a></td>
                                                <td>{{ $event->customer->name }}</td>
                                                <td><span class="badge badge-info light">{{ $event->pivot->role ?? $staff->staff_role }}</span></td>
                                                <td><span class="badge badge-success light">{{ $event->orderStatus->name ?? '-' }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No past events found.</p>
                        @endif
                    </div>

                    <!-- Attendance Tab -->
                    <div class="tab-pane fade" id="attendance" role="tabpanel">
                        @if($attendanceStats)
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h4>{{ $attendanceStats->total ?? 0 }}</h4>
                                            <p class="mb-0">Total Days</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $attendanceStats->present ?? 0 }}</h4>
                                            <p class="mb-0">Present</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $attendanceStats->absent ?? 0 }}</h4>
                                            <p class="mb-0">Absent</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h4>{{ $attendanceStats->late ?? 0 }}</h4>
                                            <p class="mb-0">Late</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('attendance.staff', $staff) }}" class="btn btn-primary">View Full Attendance History</a>
                        @else
                            <p class="text-muted">No attendance records found.</p>
                        @endif
                    </div>

                    <!-- Performance Tab -->
                    <div class="tab-pane fade" id="performance" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('staff.workload', $staff) }}" class="btn btn-primary mb-2">View Workload Report</a>
                                <a href="{{ route('staff.performance', $staff) }}" class="btn btn-info mb-2">View Performance Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

