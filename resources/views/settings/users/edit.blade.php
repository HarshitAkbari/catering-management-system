@extends('layouts.app')

@section('title', $page_title ?? 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="card-tools">
                            <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                        <h3 class="card-title mb-0">Edit {{ $page_title ?? 'User' }}</h3>
                    </div>
                    @hasPermission('users.edit')
                        @if($user->id !== auth()->id())
                            <div class="d-flex gap-2">
                                @if($user->status === 'active')
                                    <button type="button" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="showSettingsDeactivationModal('user-deactivation-modal', '{{ $user->name }}', 'user', '{{ route('users.toggle', $user) }}', 'PATCH')">
                                        <i class="bi bi-x-circle me-1"></i>Deactivate
                                    </button>
                                @else
                                    <button type="button" 
                                        class="btn btn-success btn-sm" 
                                        onclick="showSettingsActivationModal('user-activation-modal', '{{ $user->name }}', 'user', '{{ route('users.toggle', $user) }}', 'PATCH')">
                                        <i class="bi bi-check-circle me-1"></i>Activate
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endhasPermission
                </div>
                <div class="card-body">

                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('users.update', $user) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            @include('settings.users._form')

                            <div class="row mt-4">
                                <div class="col-xl-8 col-lg-10 mx-auto">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update User</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tips Section --}}
<x-tips-section>
    <x-tip-item>
        Update user roles and permissions as needed. Changes take effect immediately
    </x-tip-item>
    
    <x-tip-item>
        Leave password fields blank to keep the current password unchanged
    </x-tip-item>
    
    <x-tip-item>
        Use the activate/deactivate buttons to manage user access without deleting the account
    </x-tip-item>
    
    <x-tip-item>
        Users can be assigned multiple roles for flexible permission management
    </x-tip-item>
    
    <x-tip-item>
        Deactivated users cannot log in but their data remains in the system
    </x-tip-item>
</x-tips-section>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="user-deactivation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="user-activation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

