@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Attendance</h4>
            </div>
            <div class="card-body">
                @include('error.alerts')
                <form action="{{ route('attendance.update', $attendance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="staff_id" class="form-label">Staff <span class="text-danger">*</span></label>
                            <select class="form-control @error('staff_id') is-invalid @enderror" id="staff_id" name="staff_id" required>
                                <option value="">Select Staff</option>
                                @foreach($staffList ?? [] as $staff)
                                    <option value="{{ $staff->id }}" {{ old('staff_id', $attendance->staff_id) == $staff->id ? 'selected' : '' }}>{{ $staff->name }} ({{ $staff->staff_role }})</option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $attendance->date->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required onchange="toggleTimeInputs()">
                                <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="checkInGroup">
                            <label for="check_in_time" class="form-label">Check-in Time</label>
                            <input type="time" class="form-control @error('check_in_time') is-invalid @enderror" id="check_in_time" name="check_in_time" value="{{ old('check_in_time', $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '') }}">
                            @error('check_in_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="checkOutGroup">
                            <label for="check_out_time" class="form-label">Check-out Time</label>
                            <input type="time" class="form-control @error('check_out_time') is-invalid @enderror" id="check_out_time" name="check_out_time" value="{{ old('check_out_time', $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '') }}">
                            @error('check_out_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Attendance</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleTimeInputs() {
    const status = document.getElementById('status').value;
    const checkInGroup = document.getElementById('checkInGroup');
    const checkOutGroup = document.getElementById('checkOutGroup');
    
    if (status === 'absent') {
        checkInGroup.style.display = 'none';
        checkOutGroup.style.display = 'none';
    } else {
        checkInGroup.style.display = 'block';
        checkOutGroup.style.display = 'block';
    }
}

toggleTimeInputs();
</script>
@endsection

