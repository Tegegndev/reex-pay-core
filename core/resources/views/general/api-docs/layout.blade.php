<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', __('API Documentation')) | {{ setting('site_name', 'REEXPAY') }}</title>
    <meta name="description" content="@yield('description', __('Comprehensive API documentation for REEXPAY payment gateway integration'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset(setting('favicon')) }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ asset('general/css/google-fonts-inter-jetbrains.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('general/css/fontawesome.min.css') }}">
    
    <!-- Bootstrap -->
    <link href="{{ asset('general/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Highlight.js for syntax highlighting (more reliable than Prism.js) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/tomorrow-night.min.css">
    
    <!-- Custom Styles -->
    <link href="{{ asset('general/css/api-docs.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="api-docs-body">
    <!-- Navigation Header -->
    <nav class="api-navbar navbar navbar-expand-lg fixed-top">
        <div class="container-fluid container-lg">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center mx-2" href="{{ route('home') }}">
                <div>
                    <span class="brand-name">{{ setting('site_title', 'REEXPAY LIMITED') }}</span>
                    <small class="brand-subtitle d-block">{{ __('API Documentation') }}</small>
                </div>
            </a>
            
            <!-- Mobile Toggle -->
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-light d-lg-none me-2" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home me-1"></i>
                    <span class="d-none d-sm-inline">{{ __('Back to Site') }}</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid container-lg">
        <!-- Main Layout -->
        <div class="api-layout">
            
            <!-- Sidebar -->
            @include('general.api-docs.partials.sidebar-nav')
            
            <!-- Main Content -->
            <main class="api-content">
                <div class="py-5">
                    @yield('content')
                </div>
            </main>
        </div>
        
        <!-- Scroll to Top -->
        <button class="scroll-top-btn" id="scrollTopBtn">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('general/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Highlight.js - Simple and Reliable Syntax Highlighting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
    
    <!-- Custom Scripts -->
    @include('general.api-docs.partials._script')
    
    @stack('scripts')
</body>
</html>
