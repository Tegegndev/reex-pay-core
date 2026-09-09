<head>
    {{-- Meta Tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Page Title --}}
    <title>{{ setting('site_title') }} | @yield('title')</title>
    
    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon')) }}" type="image/x-icon"/>
    
    {{-- Core Vendor CSS --}}
    <link rel="stylesheet" href="{{ asset('general/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('general/css/chartjs.css') }}">
    <link rel="stylesheet" href="{{ asset('general/css/simple-notify.min.css') }}">
    <link rel="stylesheet" href="{{ asset('general/css/daterangepicker.css') }}">
    
    {{-- Plugin CSS --}}
    <link rel="stylesheet" href="{{ asset('backend/css/summernote-lite.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/tagify.css') }}">
    
    {{-- Application CSS --}}
    <link rel="stylesheet" href="{{ asset('general/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/custom.css?v=' . config('app.version')) }}"/>
    
    {{-- Page Specific Styles --}}
    @yield('styles')
    @stack('styles')
</head>


