<!DOCTYPE html>
<html lang="en">
    @include('frontend.layouts.user.partials._head')
    <style>
        body:has(#RegisterContainer) { background: #f4f7f2; }
        body:has(#RegisterContainer) .auth-content { min-height: 100vh; background: radial-gradient(circle at 8% 8%, #dff0c9 0, transparent 30%), radial-gradient(circle at 92% 85%, #cbe8df 0, transparent 28%), linear-gradient(145deg, #f4f7f2, #e7f1eb); }
        body:has(.login-card) { background: #f4f7f2; }
        body:has(.login-card) .auth-content { min-height: 100vh; background: radial-gradient(circle at 8% 8%, #dff0c9 0, transparent 30%), radial-gradient(circle at 92% 85%, #cbe8df 0, transparent 28%), linear-gradient(145deg, #f4f7f2, #e7f1eb); }
        body:has(.login-card) .auth-page { min-height: 100vh; padding: 48px 16px; }
        body:has(.login-card) .login-card { width: min(100%, 440px); padding: 0 !important; border: 1px solid #dce9df; border-radius: 14px !important; background: rgba(255,255,255,.97); box-shadow: 0 24px 70px rgba(20,65,43,.12) !important; overflow: hidden; }
        body:has(.login-card) .login-card > .text-center { margin: 0 -1px 28px !important; padding: 34px 28px 27px; color: #fff; background: #172b25; }
        body:has(.login-card) .login-card .login-logo { display: none; }
        body:has(.login-card) .login-card > .text-center:before { content: 'REEXPAY'; display: block; margin-bottom: 24px; color: #d7f36b; font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        body:has(.login-card) .login-card > .text-center h4 { color: #fff; font-size: 27px; letter-spacing: -.4px; }
        body:has(.login-card) .login-card > .text-center p { color: #c7d8cf !important; }
        body:has(.login-card) .login-card > .text-center .badge { color: #172b25 !important; background: #d7f36b !important; border-radius: 4px; }
        body:has(.login-card) .login-card > form, body:has(.login-card) .login-card > p, body:has(.login-card) .login-card > .border-top { margin-left: 34px; margin-right: 34px; }
        body:has(.login-card) .login-card .form-label { color: #30483d; font-size: 12px; font-weight: 800; }
        body:has(.login-card) .login-card .form-control, body:has(.login-card) .login-card .input-group-text { min-height: 47px; border-color: #d8e4dc; color: #14231f; background: #fbfdfb; }
        body:has(.login-card) .login-card .form-control:focus { border-color: #147d55; box-shadow: 0 0 0 3px rgba(20,125,85,.12); }
        body:has(.login-card) .login-card .btn-primary, body:has(.login-card) .login-card .btn-success { min-height: 51px; border: 0; border-radius: 5px; background: #147d55; font-weight: 800; box-shadow: 0 8px 16px rgba(20,125,85,.14); }
        @media (max-width: 600px) { body:has(.login-card) .auth-page { padding: 24px 12px; } body:has(.login-card) .login-card > .text-center { padding: 28px 22px 23px; } body:has(.login-card) .login-card > form, body:has(.login-card) .login-card > p, body:has(.login-card) .login-card > .border-top { margin-left: 22px; margin-right: 22px; } }
        body:has(#RegisterContainer) .auth-content > .min-vh-100 { padding: 48px 16px; }
        body:has(#RegisterContainer) .auth-content > .min-vh-100 > .bg-white { width: min(100%, 720px); padding: 0 !important; border: 1px solid #dce9df; border-radius: 14px !important; background: rgba(255,255,255,.96) !important; box-shadow: 0 24px 70px rgba(20, 65, 43, .12) !important; overflow: hidden; }
        body:has(#RegisterContainer) #RegisterContainer { padding: 40px 48px 18px !important; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4 { position: relative; margin: -40px -48px 30px !important; padding: 34px 30px 27px; background: #172b25; color: #fff; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4 img { display: none; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4:before { content: 'REEXPAY'; display: block; margin-bottom: 24px; color: #d7f36b; font-size: 20px; font-weight: 900; letter-spacing: 2px; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4 h4 { margin-top: 0 !important; font-size: 27px; font-weight: 800; letter-spacing: -.4px; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4 p { color: #c7d8cf !important; }
        body:has(#RegisterContainer) #RegisterContainer > .mb-4 .badge { color: #172b25 !important; background: #d7f36b !important; border-radius: 4px; }
        body:has(#RegisterContainer) .form-label { margin-bottom: 7px; color: #30483d; font-size: 12px; font-weight: 800; }
        body:has(#RegisterContainer) .form-control, body:has(#RegisterContainer) .form-select, body:has(#RegisterContainer) .input-group-text { min-height: 47px; border-color: #d8e4dc; border-radius: 5px; color: #14231f; background: #fbfdfb; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
        body:has(#RegisterContainer) .form-control:hover, body:has(#RegisterContainer) .form-select:hover { border-color: #a9c7b5; background: #fff; }
        body:has(#RegisterContainer) .form-control:focus, body:has(#RegisterContainer) .form-select:focus { border-color: #147d55; box-shadow: 0 0 0 3px rgba(20,125,85,.12); }
        body:has(#RegisterContainer) .btn-primary, body:has(#RegisterContainer) .btn-success { min-height: 51px; border: 0; border-radius: 5px; color: #fff; background: #147d55; font-weight: 800; letter-spacing: .2px; box-shadow: 0 8px 16px rgba(20,125,85,.14); }
        body:has(#RegisterContainer) .alert { border: 0; border-radius: 6px; font-size: 13px; }
        body:has(#RegisterContainer) .auth-content > .min-vh-100 > .bg-white > p, body:has(#RegisterContainer) .auth-content > .min-vh-100 > .bg-white > .border-top { padding-left: 40px; padding-right: 40px; }
        @media (max-width: 600px) { body:has(#RegisterContainer) #RegisterContainer { padding: 32px 22px 10px !important; } body:has(#RegisterContainer) #RegisterContainer > .mb-4 { margin: -32px -22px 25px !important; padding: 28px 22px 23px; } body:has(#RegisterContainer) #RegisterContainer > .mb-4 h4 { font-size: 23px; } body:has(#RegisterContainer) .auth-content > .min-vh-100 > .bg-white > p, body:has(#RegisterContainer) .auth-content > .min-vh-100 > .bg-white > .border-top { padding-left: 22px; padding-right: 22px; } }
    </style>
<body>


{{--<< Dynamic Content Show Here >>--}}
<div class="auth-content bg-gray">
    @yield('auth-content')
</div>




{{-- Notifications & Helpers --}}
<script src="{{ asset('frontend/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('general/js/simple-notify.min.js') }}"></script>
<script src="{{ asset('general/js/helpers.js?v=' . config('app.version')) }}"></script>

{{-- Auth Script --}}
<script src="{{ asset('frontend/js/auth.js')}}"></script>

{{-- Global Notify Configuration --}}
@include('general._notify_evs')

{{-- Page Specific Scripts --}}
@yield('scripts')
@stack('scripts')
</body>
</html>

