@extends('backend.auth.index')
@section('title', __('Login'))
@section('auth-content')
    <p class="text-muted mb-2">{{ __('Sign In to your account') }}</p>
    <form action="{{ route('admin.login') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('E-mail address') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                  <i class="fa-solid fa-envelope"></i>
                </span>
                <input class="form-control" type="email"
                       name="email"
                       placeholder="{{ __('Email') }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Password') }}</label>
            <div class="input-group">
                <span class="input-group-text">
                   <i class="fa-sharp fa-solid fa-lock"></i>
                </span>
                <input class="form-control"
                       placeholder="{{ __('Password') }}"
                       id="password-field" type="password"  name="password" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>

            <div class="col-6 text-end">
                <a href="{{ route('admin.forget.password.now') }}"
                   class="text-muted text-decoration-none">{{ __('Forgot password?') }}</a>
            </div>
        </div>
        @if(config('services.recaptcha.status'))
            <div class="g-recaptcha mt-4 mb-4" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
        @endif
        <button class="btn btn-primary w-100" type="submit">
            <x-icon name="login" class="icon"/>
            {{ __('Login') }}
        </button>
    </form>
@endsection
@push('scripts')
    <script async src="https://www.google.com/recaptcha/api.js"></script>
@endpush