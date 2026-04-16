{{-- Extends layout --}}
@extends('layout.fullwidth')

{{-- Content --}}
@section('content')
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
					<div class="text-center mb-4">
						<a href="{{ route('dashboard') }}">
							<img src="{{ asset('images/catering-logo.png') }}" alt="Brand Logo" style="max-width: 200px; height: auto;">
						</a>
						<h1 class="text-center fs-24 font-w800 text-gray-800">Catering Pro</h3>
					</div>
                    <hr>
                        <h4 class="text-center fs-20 font-w800 text-gray-800">Reset Password</h4>
                        
                        @if (session('status'))
                            <x-alert type="success" message="{{ session('status') }}" />
                        @endif

                        <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate>
                            @csrf
                            
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" required autofocus>
                                <div class="invalid-feedback">Please enter an email address.</div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                <div class="invalid-feedback">Please enter a password.</div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                <div class="invalid-feedback">Please confirm your password.</div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p><a class="text-primary" href="{{ route('login') }}">Back to Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

