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
        <label class="form-label" for="password">{{ $isEdit ? 'New Password' : 'Password' }}
            @if(!$isEdit)
                <span class="text-danger">*</span>
            @else
                <span class="text-muted small">(Leave blank to keep current password)</span>
            @endif
        </label>
        <input type="password" class="form-control" id="password" name="password" 
            placeholder="Enter password.." {{ !$isEdit ? 'required' : '' }}>
        <div class="invalid-feedback">
            Please enter a password.
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    @if(!$isEdit)
    <div class="col-md-6 mb-4">
        <label class="form-label" for="password_confirmation">Confirm Password
            <span class="text-danger">*</span>
        </label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
            placeholder="Confirm password.." required>
        <div class="invalid-feedback">
            Please confirm the password.
        </div>
    </div>
    @else
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
        <select class="form-select" id="role" name="role" required>
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

    <div class="col-md-6 mb-4">
        <label class="form-label" for="status">Status
            <span class="text-danger">*</span>
        </label>
        <select class="form-select" id="status" name="status" required>
            <option value="">Select status</option>
            <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $user->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <div class="invalid-feedback">
            Please select a status.
        </div>
        @error('status')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    @if(isset($roles) && $roles->count() > 0)
    <div class="col-12 mb-4">
        <label class="form-label">Additional Roles</label>
        <div class="row">
            @foreach($roles as $role)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $role->id }}" 
                            id="role_{{ $role->id }}" 
                            {{ (isset($user) && $user->roles->contains($role->id)) || in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ $role->display_name ?? $role->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        <small class="text-muted">Select additional roles to assign to this user. The primary role above will be synced automatically.</small>
        @error('role_ids')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    @endif
</div>

