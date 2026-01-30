@extends('layouts.app')

@section('title', $page_title ?? 'Attendance')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h4 class="card-title mb-0">{{ $page_title ?? 'Attendance' }}</h4>
                        @if(isset($subtitle))
                            <h6 class="text-muted mb-0 mt-2">{{ $subtitle }}</h6>
                        @endif
                    </div>
                    {{-- @hasPermission('attendance.create')
                    <div class="d-flex gap-2">
                        <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Mark Attendance
                        </a>
                        <a href="{{ route('attendance.bulk') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-list-check"></i> Bulk Mark
                        </a>
                    </div>
                    @endhasPermission --}}
                </div>
            </div>
            <div class="card-body">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $todayPresent ?? 0 }}</h3>
                                <p class="mb-0">Present Today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3>{{ $todayAbsent ?? 0 }}</h3>
                                <p class="mb-0">Absent Today</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('attendance.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $filterValues['start_date'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $filterValues['end_date'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="staff_filter" class="form-label">Staff</label>
                            <select name="staff_id" id="staff_filter" class="form-control form-control-sm">
                                <option value="">All Staff</option>
                                @foreach($staffList ?? [] as $staff)
                                    <option value="{{ $staff->id }}" {{ ($filterValues['staff_id'] ?? '') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="status_filter" class="form-label">Status</label>
                            <select name="status" id="status_filter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="present" {{ ($filterValues['status'] ?? '') == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ ($filterValues['status'] ?? '') == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ ($filterValues['status'] ?? '') == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ ($filterValues['status'] ?? '') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                        </div>
                    </div>
                    <x-filter-buttons resetRoute="{{ route('attendance.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="datatable table table-sm mb-0 table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Staff Name</th>
                                <th>Status</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Notes</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>{{ $attendance->staff->name }}</td>
                                    <td>
                                        @if($attendance->status === 'present')
                                            <span class="badge badge-success light">Present</span>
                                        @elseif($attendance->status === 'absent')
                                            <span class="badge badge-danger light">Absent</span>
                                        @elseif($attendance->status === 'late')
                                            <span class="badge badge-warning light">Late</span>
                                        @else
                                            <span class="badge badge-info light">Half Day</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->notes ?? '-' }}</td>
                                    <td class="text-end">
                                        <x-edit-button module="attendance" route="attendance.edit" :model="$attendance" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <p class="text-muted">No attendance records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

