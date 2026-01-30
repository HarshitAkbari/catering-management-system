@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Roles & Permissions</h4>
                    <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary btn-add">Add New Role</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-striped">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Display Name</th>
                                    <th>Permission Type</th>
                                    <th>Write Permissions</th>
                                    <th>Menus</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>{{ ucfirst($role->name) }}</td>
                                        <td>{{ $role->display_name ?? '-' }}</td>
                                        <td>
                                            @if($role->permission_type)
                                                <span class="badge badge-{{ $role->permission_type === 'read' ? 'info' : 'success' }}">
                                                    {{ ucfirst($role->permission_type) }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($role->permission_type === 'write' && $role->write_permissions)
                                                @foreach($role->write_permissions as $perm)
                                                    <span class="badge badge-primary">{{ ucfirst($perm) }}</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $role->menus->count() }} menus</td>
                                        <td>
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            <x-delete-button 
                                                item-name="{{ $role->display_name ?? $role->name }}"
                                                delete-url="{{ route('roles.destroy', $role) }}"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No roles found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-delete-modal id="deleteModal" />
@endsection
