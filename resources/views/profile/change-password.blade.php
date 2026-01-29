@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="card-tools">
                            <a href="{{ route('dashboard') }}" class="btn btn-default btn-sm">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                        <h3 class="card-title mb-0">Change Password</h3>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Your password must be at least 8 characters long
                        </x-tip-item>
                        
                        <x-tip-item>
                            Use a strong password with a combination of letters, numbers, and special characters
                        </x-tip-item>
                        
                        <x-tip-item>
                            Make sure your new password is different from your current password
                        </x-tip-item>
                        
                        <x-tip-item>
                            After changing your password, you'll need to log in again with your new password
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('change-password.update') }}" method="POST" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="current_password">Current Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                        class="form-control @error('current_password') is-invalid @enderror" 
                                        id="current_password" 
                                        name="current_password" 
                                        placeholder="Enter current password.." 
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter your current password.
                                    </div>
                                    @error('current_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="password">New Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Enter new password.." 
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a new password.
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="password_confirmation">Confirm New Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        placeholder="Confirm new password.." 
                                        required>
                                    <div class="invalid-feedback">
                                        Please confirm your new password.
                                    </div>
                                    @error('password_confirmation')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-xl-8 col-lg-10 mx-auto">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
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
@endsection

