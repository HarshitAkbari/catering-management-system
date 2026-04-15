{{-- Extends layout --}}
@extends('layout.fullwidth')

{{-- Content --}}
@section('content')
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
						<div class="text-center mb-3">
							<a href="{{ route('dashboard') }}"><img src="{{ asset('images/logo-full.png') }}" alt=""></a>
						</div>
                        <h4 class="text-center mb-4">Sign up your account</h4>
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="mb-1"><strong>Business Name</strong></label>
                                <input type="text" name="tenant_name" class="form-control @error('tenant_name') is-invalid @enderror" value="{{ old('tenant_name') }}" required autofocus>
                                @error('tenant_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="mb-1"><strong>Your Name</strong></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="mb-1"><strong>Email</strong></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="mb-1"><strong>Password</strong></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="mb-1"><strong>Confirm Password</strong></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-block">Sign up</button>
                            </div>
                        </form>
                        <div class="new-account mt-3">
                            <p>Already have an account? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
