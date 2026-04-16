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
                <form class="needs-validation" action="{{ route('attendance.update', $attendance) }}" method="POST" novalidate>
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
                            <div class="invalid-feedback">Please select a staff member.</div>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $attendance->date->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                            <div class="invalid-feedback">Please select a date.</div>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="half_day" {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                            @error('status')
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

@endsection

