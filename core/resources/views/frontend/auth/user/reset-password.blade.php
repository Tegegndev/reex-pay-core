@extends('frontend.layouts.auth')

@section('title', __('User Reset Password'))

@section('auth-content')
	<div class="auth-page d-flex align-items-center justify-content-center">
		<div class="login-card rounded shadow p-4">
			
			{{-- Branding --}}
			<div class="text-center mb-4">
				<img src="{{ asset(setting('logo')) }}" alt="Logo" class="img-fluid mb-2 login-logo">
                <h5 class="text-primary mb-2">{{ __('User Password Reset') }}</h5>
				<p class="text-muted mb-0">
					@lang("Enter your new password and confirm it to reset your account password.")
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
			
			{{-- Reset Password Form --}}
			<form action="{{ route('user.password.store') }}" method="POST">
				@csrf
				
				{{-- Hidden Token --}}
				<input type="hidden" name="token" value="{{ $request->route('token') }}">
				
				{{-- Email --}}
				<div class="mb-3">
					<label for="email" class="form-label fw-semibold">@lang('Email Address')</label>
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
							value="{{ old('email', $request->email) }}"
							required
							autocomplete="email"
						>
					</div>
				</div>
				
				{{-- New Password --}}
				<div class="mb-3">
					<label for="password" class="form-label fw-semibold">@lang('New Password')</label>
					<div class="input-group">
					<span class="input-group-text">
						<i class="fas fa-lock"></i>
					</span>
						<input
							type="password"
							id="password"
							name="password"
							class="form-control"
							placeholder="@lang('Enter new password')"
							required
							autocomplete="new-password"
						>
					</div>
				</div>
				
				{{-- Confirm Password --}}
				<div class="mb-3">
					<label for="password_confirmation" class="form-label fw-semibold">@lang('Confirm Password')</label>
					<div class="input-group">
					<span class="input-group-text">
				        <i class="fas fa-key"></i>
					</span>
						<input
							type="password"
							id="password_confirmation"
							name="password_confirmation"
							class="form-control"
							placeholder="@lang('Confirm your password')"
							required
							autocomplete="new-password"
						>
					</div>
				</div>
				
				{{-- Submit Button --}}
				<button type="submit" class="btn btn-primary w-100">
					<i class="fa-light fa-key me-1"></i>
					@lang('Reset Password')
				</button>
			</form>
            
            {{-- Back to Login Link --}}
			<p class="text-center mt-4 mb-0">
				@lang("Remember your password?")
				<a href="{{ route('user.login') }}" class="text-decoration-none text-primary fw-semibold">
					@lang('Return to Login')
				</a>
			</p>
		</div>
	</div>
@endsection
