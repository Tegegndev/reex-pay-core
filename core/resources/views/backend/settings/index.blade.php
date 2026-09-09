@extends('backend.layouts.app')
@section('title')
    {{ __('Settings') }}
@endsection
@section('content')
    @php($settingsMenu = getAdminMenuByCode('settings-management'))
    
    <div class="d-flex justify-content-between align-items-center my-2">
        <div class="fs-3 fw-semibold">@yield('setting_title')</div>
        <div class="btn-toolbar">
            @yield('setting_action')
        </div>
    </div>

    <div class="card px-3 py-3">
        @if($settingsMenu && isset($settingsMenu['sub_menus']))
            <ul class="nav nav-pills bg-light rounded p-1">
                @foreach($settingsMenu['sub_menus'] as $menu)
                    <li class="nav-item ">
                        <a class="nav-link {{ isActive($menu['route'] , $menu['params'] ?? []) }}" aria-current="page"
                           href="{{ route($menu['route'], $menu['params'] ?? []) }}">
                            <x-icon name="{{ $menu['icon'] }}" height="18" width="18"/>
                            {{ title($menu['label']) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
        <div class="py-3 mt-3">
            @yield('setting_content')
        </div>
    </div>
@endSection
