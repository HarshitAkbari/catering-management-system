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
                        <h4 class="text-center mb-4">Sign in your account</h4>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="mb-1"><strong>Email</strong></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
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
                            <div class="row d-flex justify-content-between mt-4 mb-2">
                                <div class="mb-3">
                                   <div class="form-check custom-checkbox ms-1">
                                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                        <label class="form-check-label" for="remember">Remember my preference</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <a href="{{ route('forgot-password') }}">Forgot Password?</a>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                            </div>
                        </form>
                        <div class="new-account mt-3">
                            <p>Don't have an account? <a class="text-primary" href="{{ route('register') }}">Register</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
