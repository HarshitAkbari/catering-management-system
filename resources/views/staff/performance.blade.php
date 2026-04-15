@extends('layouts.app')

@section('title', 'Staff Performance Report')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Performance Report - {{ $staff->name }}</h4>
                <a href="{{ route('staff.show', $staff) }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('staff.performance', $staff) }}" class="mb-4">
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

                @if(isset($performance))
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $performance['total_events'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Events</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $performance['attendance_rate'] ?? 0 }}%</h3>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $performance['total_attendance_days'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Days</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $performance['present_days'] ?? 0 }}</h3>
                                    <p class="mb-0">Present Days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

