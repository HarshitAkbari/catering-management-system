@extends('layouts.app')

@section('title', 'Attendance Report')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Attendance Report</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('attendance.report') }}" class="mb-4">
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
                            <button type="submit" class="btn btn-primary btn-sm">Generate Report</button>
                        </div>
                    </div>
                </form>

                @if(isset($report))
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $report['total_records'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Records</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $report['present'] ?? 0 }}</h3>
                                    <p class="mb-0">Present</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $report['absent'] ?? 0 }}</h3>
                                    <p class="mb-0">Absent</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $report['attendance_rate'] ?? 0 }}%</h3>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Half Day</th>
                                    <th>Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['by_date'] ?? [] as $date => $attendances)
                                    @php
                                        $dateAttendances = collect($attendances);
                                        $present = $dateAttendances->where('status', 'present')->count();
                                        $absent = $dateAttendances->where('status', 'absent')->count();
                                        $late = $dateAttendances->where('status', 'late')->count();
                                        $halfDay = $dateAttendances->where('status', 'half_day')->count();
                                        $total = $dateAttendances->count();
                                        $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                                        <td>{{ $present }}</td>
                                        <td>{{ $absent }}</td>
                                        <td>{{ $late }}</td>
                                        <td>{{ $halfDay }}</td>
                                        <td>{{ $rate }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

