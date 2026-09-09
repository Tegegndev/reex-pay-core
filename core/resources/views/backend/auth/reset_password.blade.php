@extends('backend.auth.index')
@section('title', __('Forget Password'))
@section('auth-content')
	<p class="text-muted mb-2">{{ __('Reset your password') }}</p>
	<form action="{{ route('admin.reset.password.submit') }}" method="post">
		@csrf
		
		<input type="hidden" name="token" value="{{ request('token') }}">
		<input type="hidden" name="email" value="{{ request('email') }}">
		
		<div class="mb-3">
			<label for="email" class="form-label">{{ __('New Password') }}</label>
			<div class="input-group">
                <span class="input-group-text">
                   <i class="fa-sharp fa-solid fa-lock"></i>
                </span>
				<input class="form-control"
				       placeholder="{{ __('New Password') }}"
				       id="password-field" type="password" name="password"  required>
			</div>
		</div>
		<div class="mb-3">
			<label for="email" class="form-label">{{ __('Confirm Password') }}</label>
			<div class="input-group">
                <span class="input-group-text">
                   <i class="fa-sharp fa-solid fa-lock"></i>
                </span>
				<input class="form-control"
				       placeholder="{{ __('Confirm Password') }}"
				       id="password-field" type="password" name="password_confirmation" required>
			</div>
		</div>
		
		
		<button class="btn btn-primary w-100" type="submit">
			<x-icon name="login" class="icon"/> {{ __('Reset Password') }}</button>
	</form>
@endsection
