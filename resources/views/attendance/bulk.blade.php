@extends('layouts.app')

@section('title', 'Bulk Mark Attendance')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bulk Mark Attendance</h4>
            </div>
            <div class="card-body">
                @include('error.alerts')
                <form action="{{ route('attendance.bulk.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm" onclick="markAllAsPresent()">Mark All as Present</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="markAllAsAbsent()">Mark All as Absent</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                                    </th>
                                    <th>Staff Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffList ?? [] as $index => $staff)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="staff-checkbox" onchange="toggleRow(this, {{ $index }})">
                                        </td>
                                        <td>{{ $staff->name }}</td>
                                        <td><span class="badge badge-info light">{{ $staff->staff_role }}</span></td>
                                        <td>
                                            <input type="hidden" name="attendance[{{ $index }}][staff_id]" value="{{ $staff->id }}">
                                            <select name="attendance[{{ $index }}][status]" class="form-control form-control-sm status-input" disabled>
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="late">Late</option>
                                                <option value="half_day">Half Day</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="time" name="attendance[{{ $index }}][check_in_time]" class="form-control form-control-sm time-input" disabled>
                                        </td>
                                        <td>
                                            <input type="time" name="attendance[{{ $index }}][check_out_time]" class="form-control form-control-sm time-input" disabled>
                                        </td>
                                        <td>
                                            <input type="text" name="attendance[{{ $index }}][notes]" class="form-control form-control-sm notes-input" placeholder="Notes" disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Save Attendance</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    checkboxes.forEach((cb, index) => {
        cb.checked = checkbox.checked;
        toggleRow(cb, index);
    });
}

function toggleRow(checkbox, index) {
    const row = checkbox.closest('tr');
    const inputs = row.querySelectorAll('.status-input, .time-input, .notes-input');
    inputs.forEach(input => {
        input.disabled = !checkbox.checked;
    });
}

function markAllAsPresent() {
    document.querySelectorAll('.staff-checkbox').forEach((cb, index) => {
        cb.checked = true;
        toggleRow(cb, index);
        const statusSelect = cb.closest('tr').querySelector('.status-input');
        statusSelect.value = 'present';
    });
}

function markAllAsAbsent() {
    document.querySelectorAll('.staff-checkbox').forEach((cb, index) => {
        cb.checked = true;
        toggleRow(cb, index);
        const statusSelect = cb.closest('tr').querySelector('.status-input');
        statusSelect.value = 'absent';
    });
}
</script>
@endsection

