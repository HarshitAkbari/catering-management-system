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
						</div>
                        <h4 class="text-center mb-4">Account Locked</h4>
                        <form method="POST" action="#" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                <div class="invalid-feedback">Please enter a password.</div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block">Unlock</button>
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


