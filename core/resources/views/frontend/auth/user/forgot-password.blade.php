@extends('frontend.layouts.auth')

@section('title', __('User Forgot Password'))

@section('auth-content')
	<div class="auth-page d-flex align-items-center justify-content-center">
		<div class="login-card rounded shadow p-4">
			
			{{-- Branding --}}
			<div class="text-center mb-4">
				<img src="{{ asset(setting('logo')) }}" alt="Logo" class="img-fluid mb-2 login-logo">
				<h5 class="text-primary mb-2">{{ __('User Password Reset') }}</h5>
				<p class="text-muted mb-0">
					@lang("Enter the email address linked to your user account, and we'll email you a link to reset your password.")
				</p>
                <div class="mt-2 mb-3">
                    <span class="badge bg-primary p-2">
                        <i class="fa-duotone fa-user me-1"></i> {{ __('Regular User Account') }}
                    </span>
                </div>
			</div>
			
			{{-- Validation Errors --}}
			@if ($errors->any())
				<div class="alert alert-warning alert-dismissible fade show" role="alert">
					@foreach ($errors->all() as $error)
						<strong>{{ $error }}</strong><br>
					@endforeach
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif
			
			{{-- Password Reset Request Form --}}
			<form action="{{ route('user.password.email') }}" method="POST">
				@csrf
				
				{{-- Email Input --}}
				<div class="mb-3">
					<label for="email" class="form-label fw-semibold">
						@lang('Email Address')
					</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fas fa-envelope"></i>
					</span>
						<input
							type="email"
							id="email"
							name="email"
							class="form-control"
							placeholder="@lang('Enter your email')"
							required
							autocomplete="email"
						>
					</div>
				</div>
				
				{{-- Submit Button --}}
				<button type="submit" class="btn btn-primary w-100">
					<i class="fa-light fa-paper-plane me-1"></i>
					@lang('Send Password Reset Link')
				</button>
			</form>
			
			{{-- Back to Login Link --}}
			<p class="text-center mt-4 mb-0">
				@lang("Don't need to reset your password?")
				<a href="{{ route('user.login') }}" class="text-decoration-none text-primary fw-semibold">
					@lang('Return to Login')
				</a>
			</p>
            
            {{-- Merchant Password Reset Link --}}
            <div class="border-top mt-4 pt-3">
                <p class="text-center mb-0 sm-mb-text">
                    {{ __("Need to reset a merchant account password?") }}
                    <a href="{{ route('merchant.password.request') }}" class="text-decoration-none text-success fw-semibold">
                        <i class="fa-duotone fa-store me-1"></i> {{ __('Merchant Password Reset') }}
                    </a>
                </p>
            </div>
		</div>
	</div>
@endsection
