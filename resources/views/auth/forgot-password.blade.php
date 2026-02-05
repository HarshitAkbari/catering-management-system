{{-- Extends layout --}}
@extends('layout.fullwidth')

{{-- Content --}}
@section('content')
    <div class="col-md-6">
        <div class="authincation-content">
            <div class="row no-gutters">
                <div class="col-xl-12">
                    <div class="auth-form">
						{{-- <div class="text-center mb-3">
							<a href="{{ route('dashboard') }}"><img src="{{ asset('images/logo-full.png') }}" alt=""></a>
						</div> --}}
                        <h4 class="text-center mb-4">Forgot Password</h4>
                        
                        @if (session('status'))
                            <x-alert type="success" message="{{ session('status') }}" />
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                                <div class="invalid-feedback">Please enter an email address.</div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
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
