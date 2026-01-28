@extends('layouts.app')

@section('title', 'Staff Workload Report')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Workload Report - {{ $staff->name }}</h4>
                <a href="{{ route('staff.show', $staff) }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('staff.workload', $staff) }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </div>
                    </div>
                </form>

                @if(isset($workload))
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $workload['total_events'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Events</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $workload['events_per_week'] ?? 0 }}</h3>
                                    <p class="mb-0">Events Per Week</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($workload['events']) && $workload['events']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Event Date</th>
                                        <th>Order Number</th>
                                        <th>Customer</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workload['events'] as $event)
                                        <tr>
                                            <td>{{ $event->event_date->format('M d, Y') }}</td>
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
                        <p class="text-muted">No events found for this period.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

