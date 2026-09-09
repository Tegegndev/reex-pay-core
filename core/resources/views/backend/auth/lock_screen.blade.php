@extends('backend.auth.index')
@section('title', __('Account Locked'))
@section('auth-content')
    <p class="text-muted mb-2">{{ __('Enter your password to unlock') }}</p>
    <form action="{{ route('admin.lock-screen.unlock') }}" method="post">
        @csrf
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <x-icon name="cil-lock-locked" class="icon"/>
                </span>
                <input class="form-control" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" id="password-field" type="password" name="password" required>
            </div>
        </div>
        <button class="btn btn-primary w-100" type="submit"> <x-icon name="unlock" class="icon"/> {{ __('Unlock') }}</button>
    </form>
@endsection
