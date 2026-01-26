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
                        <h4 class="text-center mb-4">Forgot Password</h4>
                        
                        @if (session('status'))
                            <x-alert type="success" message="{{ session('status') }}" />
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label><strong>Email</strong></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
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
