<header class="header header-sticky p-0 mb-4">
    <div class="container-fluid border-bottom px-4">
        <button class="header-toggler" type="button"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"
                style="margin-inline-start: -14px;">
            <x-icon name="cil-menu" class="icon icon-lg"/>
        </button>
        @include('backend.layouts.partials._search')
        <ul class="header-nav ms-auto">

{{--             Admin notifications dropdown--}}
            <li class="nav-item pe-2 dropdown" id="append-new-admin-notification">
                @include('backend.layouts.partials._notifications', ['notifications' => auth()->user()->getRecentNotifications()])
            </li>
            
            <li class="nav-item">
                <a class="nav-link"
                   data-coreui-toggle="tooltip"
                   data-coreui-title="@lang('Control Panel')"
                   href="{{ route('admin.app.control-panel') }}">
                    <x-icon name="apps-1" class="icon icon-lg"/>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link"
                   data-coreui-toggle="tooltip"
                   data-coreui-title="Visit Landing Page"
                   href="{{ route('home') }}" target="_blank">
                    <x-icon name="cil-laptop" class="icon icon-lg"/>
                </a>
            </li>
        </ul>
        <ul class="header-nav">
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            <li class="nav-item dropdown">
                <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button"
                        aria-expanded="false" data-coreui-toggle="dropdown">
                    <x-icon name="cil-language" class="icon icon-lg theme-icon-active"/>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                    @foreach($languages as $language)
                        <li>
                            <a href="{{ route('locale-set', $language->code) }}"
                               class="dropdown-item d-flex align-items-center {{ $language->code == app()->getLocale() ? 'active' : '' }}">
                                <img class="icon me-2" src="{{ asset($language->flag) }}" alt="{{ $language->name }}">
                                {{ $language->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="avatar avatar-md"><img class="avatar-img" src="{{ asset(auth()->user()->avatar_alt) }}"
                                                       alt="{{ auth()->user()->name }}"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">{{ __('Account') }}</div>
                    <a class="dropdown-item" href="{{ route('admin.profile.view') }}">
                        <x-icon name="user-cog-1" class="icon me-2"/> {{ __('Profile Settings') }}
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="{{ route('admin.lock') }}">
                        <x-icon name="cil-lock-locked" class="icon me-2"/>{{ __('Lock Account') }}
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <x-icon name="cil-account-logout" class="icon me-2"/> {{ __('Logout') }}</button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</header>
