@php
    $isEdit = isset($user) && $user->exists;
    $formAction = $isEdit ? route('users.update', $user) : route('users.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Name
            <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control" id="name" name="name" 
            placeholder="Enter user name.." value="{{ old('name', $user->name ?? '') }}" required>
        <div class="invalid-feedback">
            Please enter a name.
        </div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="email">Email
            <span class="text-danger">*</span>
        </label>
        <input type="email" class="form-control" id="email" name="email" 
            placeholder="Enter email address.." value="{{ old('email', $user->email ?? '') }}" required>
        <div class="invalid-feedback">
            Please enter a valid email address.
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="mobile">Mobile Number
            <span class="text-danger mobile-required-indicator" style="display: none;">*</span>
        </label>
        <input type="text" class="form-control" id="mobile" name="mobile" 
            placeholder="Enter mobile number.." value="{{ old('mobile', $user->mobile ?? '') }}">
        <div class="invalid-feedback">
            Please enter a mobile number.
        </div>
        @error('mobile')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        <small class="text-muted">Required for Staff and Manager roles</small>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control" id="address" name="address" rows="3" 
            placeholder="Enter address..">{{ old('address', $user->address ?? '') }}</textarea>
        @error('address')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    @if($isEdit)
    <div class="col-md-6 mb-4">
        <label class="form-label" for="password">New Password
            <span class="text-muted small">(Leave blank to keep current password)</span>
        </label>
        <input type="password" class="form-control" id="password" name="password" 
            placeholder="Enter password..">
        <div class="invalid-feedback">
            Please enter a password.
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="password_confirmation">Confirm New Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
            placeholder="Confirm new password..">
    </div>
    @endif

    <div class="col-md-6 mb-4">
        <label class="form-label" for="role">Role
            <span class="text-danger">*</span>
        </label>
        <select class="form-control single-select" id="role" name="role" required>
            <option value="">Select a role</option>
            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ old('role', $user->role ?? '') === 'manager' ? 'selected' : '' }}>Manager</option>
            <option value="staff" {{ old('role', $user->role ?? '') === 'staff' ? 'selected' : '' }}>Staff</option>
        </select>
        <div class="invalid-feedback">
            Please select a role.
        </div>
        @error('role')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-4 staff-role-field" id="staff-role-field" style="display: none;">
        <label class="form-label" for="staff_role_id">Staff Role
            <span class="text-danger">*</span>
        </label>
        <select class="form-control single-select" id="staff_role_id" name="staff_role_id">
            <option value="">Select a staff role</option>
            @if(isset($staffRoles) && $staffRoles->count() > 0)
                @foreach($staffRoles as $staffRole)
                    <option value="{{ $staffRole->id }}" 
                        {{ old('staff_role_id', $staffRoleId ?? '') == $staffRole->id ? 'selected' : '' }}>
                        {{ $staffRole->name }}
                    </option>
                @endforeach
            @endif
        </select>
        <div class="invalid-feedback">
            Please select a staff role.
        </div>
        @error('staff_role_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
        <small class="text-muted">Select the staff role from settings</small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const mobileInput = document.getElementById('mobile');
    const mobileRequiredIndicator = document.querySelector('.mobile-required-indicator');
    const staffRoleField = document.getElementById('staff-role-field');
    const staffRoleSelect = document.getElementById('staff_role_id');
    
    function toggleStaffFields() {
        const role = roleSelect.value;
        if (role === 'staff' || role === 'manager') {
            mobileInput.setAttribute('required', 'required');
            if (mobileRequiredIndicator) {
                mobileRequiredIndicator.style.display = 'inline';
            }
            if (staffRoleField) {
                staffRoleField.style.display = 'block';
                staffRoleSelect.setAttribute('required', 'required');
            }
        } else {
            mobileInput.removeAttribute('required');
            if (mobileRequiredIndicator) {
                mobileRequiredIndicator.style.display = 'none';
            }
            if (staffRoleField) {
                staffRoleField.style.display = 'none';
                staffRoleSelect.removeAttribute('required');
                staffRoleSelect.value = '';
            }
        }
    }
    
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleStaffFields);
        toggleStaffFields(); // Check on page load
    }
});
</script>

