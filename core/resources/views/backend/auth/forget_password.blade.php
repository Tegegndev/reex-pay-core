@extends('backend.auth.index')
@section('title', __('Forget Password'))
@section('auth-content')
    <p class="text-muted mb-2">{{ __("Enter the email address associated with your account and we'll send you a link to reset your password.") }}</p>
    <form action="{{ route('admin.forget.password.submit') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="input-group">
                 <span class="input-group-text">
                      <i class="fa-solid fa-envelope"></i>
                </span>
                <input class="form-control" type="email" name="email"  placeholder="{{ __('Email') }}" required>
            </div>
        </div>
        <div class="row mb-3">
            <a href="{{ route('admin.login') }}" class="text-muted text-decoration-none">{{ __('Return to login') }}</a>
        </div>

        <button class="btn btn-primary w-100" type="submit"> <x-icon name="login" class="icon"/> {{ __('Continue') }}</button>
    </form>
@endsection
