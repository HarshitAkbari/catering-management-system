@extends('layouts.app')

@section('title', 'Edit Profile')

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
                        <h3 class="card-title mb-0">Edit Profile</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('profile.update') }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="name">Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="name" name="name" 
                                        placeholder="Enter your name.." 
                                        value="{{ old('name', $user->name ?? '') }}" 
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter your name.
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="email">Email
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                        id="email" name="email" 
                                        placeholder="Enter email address.." 
                                        value="{{ old('email', $user->email ?? '') }}" 
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-xl-8 col-lg-10 mx-auto">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
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
        Update your profile information to keep your account details current
    </x-tip-item>
    
    <x-tip-item>
        Your email address is used for login and important notifications
    </x-tip-item>
    
    <x-tip-item>
        To change your password, use the "Change Password" option in the profile menu
    </x-tip-item>
</x-tips-section>
@endsection

