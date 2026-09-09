@extends('backend.auth.index')
@section('title', __('Two-Factor Authentication'))
@section('auth-content')
	<h4 class="fw-bold mb-2">{{ __('Verify Your Identity') }}</h4>
	<p class="text-muted">
		{{ __('Enter the 6-digit code from your Google Authenticator app to complete your login.') }}
	</p>
	<form action="{{ route('admin.two-factor-challenge') }}" method="post">
		@csrf
		<div class="mb-3">
			<label for="verification_code" class="form-label">{{ __('Enter Authentication Code') }}</label>
			<div class="input-group">
                <span class="input-group-text">
                   <i class="fa-sharp fa-solid fa-lock"></i>
                </span>
				<input class="form-control" placeholder="••••••"  id="verification_code"
				       type="password" name="verification_code"
				       title="{{ __('Enter the 6-digit code from your authenticator app') }}" required >
			</div>
			@error('verification_code')
			<span class="text-danger">{{ $message }}</span>
			@enderror
		</div>
		
		<button class="btn btn-primary w-100" type="submit">
			<i class="fas fa-check-circle"></i> {{ __('Verify & Proceed') }}
		</button>
	</form>
@endsection
