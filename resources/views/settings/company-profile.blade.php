@extends('layouts.app')

@section('title', 'Company Profile')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Company Profile</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Company Profile</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('settings.company-profile.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="name" required value="{{ old('name', $tenant->name) }}" class="form-control">
                                    @error('name')
                                        <p class="text-danger small mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" required value="{{ old('email', $tenant->email) }}" class="form-control">
                                    @error('email')
                                        <p class="text-danger small mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="form-control">
                                    @error('phone')
                                        <p class="text-danger small mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Logo URL</label>
                                    <input type="url" name="logo_url" value="{{ old('logo_url', $tenant->logo_url) }}" class="form-control">
                                    @error('logo_url')
                                        <p class="text-danger small mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="3" class="form-control">{{ old('address', $tenant->address) }}</textarea>
                                    @error('address')
                                        <p class="text-danger small mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('settings.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

