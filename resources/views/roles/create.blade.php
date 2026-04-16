@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Create Role</h4>
                    <a href="{{ route('roles.index') }}" class="btn btn-dark btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Role Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Role Type <span class="text-danger">*</span></label>
                                <select name="name" id="name" class="form-select" required>
                                    <option value="">Select Role Type</option>
                                    <option value="manager" {{ old('name') === 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ old('name') === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                <small class="form-text text-muted">Note: Only Manager and Staff roles can be configured. Admin has full access by default.</small>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" name="display_name" id="display_name" class="form-control" value="{{ old('display_name') }}" placeholder="e.g., Manager - Sales">
                                @error('display_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permission Type -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Permission Type <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="permission_type" id="permission_type_read" value="read" {{ old('permission_type') === 'read' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="permission_type_read">
                                        Read - User can only view data
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="permission_type" id="permission_type_write" value="write" {{ old('permission_type') === 'write' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="permission_type_write">
                                        Write - User can perform actions (select actions below)
                                    </label>
                                </div>
                                @error('permission_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Write Permissions (conditional) -->
                        <div class="row mb-3" id="write_permissions_section" style="display: none;">
                            <div class="col-md-12">
                                <label class="form-label">Write Permissions</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="write_permissions[]" id="write_add" value="add" {{ in_array('add', old('write_permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="write_add">Add</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="write_permissions[]" id="write_edit" value="edit" {{ in_array('edit', old('write_permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="write_edit">Edit</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="write_permissions[]" id="write_delete" value="delete" {{ in_array('delete', old('write_permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="write_delete">Delete</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="write_permissions[]" id="write_export" value="export" {{ in_array('export', old('write_permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="write_export">Export</label>
                                </div>
                                @error('write_permissions')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Menus Selection -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Menus <span class="text-danger">*</span></label>
                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    @if($parentMenus->isEmpty())
                                        <div class="alert alert-warning">
                                            <strong>No menus found!</strong> Please seed the menus first by running: 
                                            <code>php artisan db:seed --class=MenuSeeder</code>
                                        </div>
                                    @else
                                        @foreach($parentMenus as $parentMenu)
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input parent-menu" type="checkbox" name="menu_ids[]" id="menu_{{ $parentMenu->id }}" value="{{ $parentMenu->id }}" {{ in_array($parentMenu->id, old('menu_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="menu_{{ $parentMenu->id }}">
                                                    {{ $parentMenu->display_name }}
                                                </label>
                                            </div>
                                            @php
                                                $childMenus = $menus->where('parent_id', $parentMenu->id);
                                            @endphp
                                            @if($childMenus->count() > 0)
                                                <div class="ms-4 mt-2">
                                                    @foreach($childMenus as $childMenu)
                                                        <div class="form-check">
                                                            <input class="form-check-input child-menu" type="checkbox" name="menu_ids[]" id="menu_{{ $childMenu->id }}" value="{{ $childMenu->id }}" data-parent="{{ $parentMenu->id }}" {{ in_array($childMenu->id, old('menu_ids', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="menu_{{ $childMenu->id }}">
                                                                {{ $childMenu->display_name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                @error('menu_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Create Role</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const permissionTypeRadios = document.querySelectorAll('input[name="permission_type"]');
    const writePermissionsSection = document.getElementById('write_permissions_section');
    
    // Show/hide write permissions based on permission type
    function toggleWritePermissions() {
        const writeSelected = document.getElementById('permission_type_write').checked;
        writePermissionsSection.style.display = writeSelected ? 'block' : 'none';
    }
    
    permissionTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleWritePermissions);
    });
    
    // Initialize on page load
    toggleWritePermissions();
    
    // Parent menu checkbox logic
    document.querySelectorAll('.parent-menu').forEach(parentCheckbox => {
        parentCheckbox.addEventListener('change', function() {
            const parentId = this.value;
            const childCheckboxes = document.querySelectorAll(`.child-menu[data-parent="${parentId}"]`);
            childCheckboxes.forEach(child => {
                child.checked = this.checked;
            });
        });
    });
});
</script>
@endsection

