<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $staff->name ?? '') }}" required>
        <div class="invalid-feedback">Please enter a name.</div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $staff->phone ?? '') }}" required>
        <div class="invalid-feedback">Please enter a phone number.</div>
        @error('phone')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $staff->email ?? '') }}">
        <div class="invalid-feedback">Please enter a valid email address.</div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="staff_role_id">Role <span class="text-danger">*</span></label>
        <select class="form-control @error('staff_role_id') is-invalid @enderror" id="staff_role_id" name="staff_role_id" required>
            <option value="">Select Role</option>
            @foreach($roles ?? [] as $role)
                <option value="{{ $role->id }}" {{ old('staff_role_id', $staff->staff_role_id ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        <div class="invalid-feedback">Please select a role.</div>
        @error('staff_role_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        @error('staff_role')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    @if(isset($staff) && $staff->exists)
    <div class="col-md-6 mb-4">
        <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <div class="invalid-feedback">Please select a status.</div>
        @error('status')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    @endif
    <div class="col-md-12 mb-4">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $staff->address ?? '') }}</textarea>
        @error('address')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

