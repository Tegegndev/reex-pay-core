<header id="header-sticky" class="header-1 @if(isset($isBreadcrumb) && $isBreadcrumb) style-2 @endif">
    <div class="container">
        <div class="mega-menu-wrapper">
            <div class="header-main">
                <div class="header-left">
                    <div class="logo">
                        @if(isset($isBreadcrumb) && $isBreadcrumb)
                            <a href="{{ route('home') }}" class="header-logo">
	                            <img src="{{ asset(setting('light_logo')) }}" alt="logo-img" loading="lazy">
                            </a>
                        @endif
                        <a href="{{ route('home') }}" class="header-logo-2">
	                        <img src="{{ asset(setting('logo')) }}" alt="logo-img" loading="lazy">
                        </a>
                    </div>
                </div>
                <div class="header-right d-flex justify-content-end align-items-center">
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav>
                                @include('frontend.layouts.partials._menu_list')
                            </nav>
                        </div>
                    </div>
                    <div class="header-button d-flex align-items-center justify-content-center gap-4">
                       @if(auth()->check())
                            <a href="{{ route('user.dashboard') }}" class="theme-btn" >
                                {{ __('Dashboard') }}
                            </a>
                       @else
                            <a class="theme-btn style-border" href="{{ route('user.register') }}" >
                                {{ __('Register') }}
                            </a>
                            <a href="{{ route('user.login') }}" class="theme-btn" >
                                {{ __('Account Login') }}
                            </a>
                       @endif

                    </div>
                    <div class="header__hamburger">
                        <div class="sidebar__toggle">
                            <i class="fas fa-bars"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
