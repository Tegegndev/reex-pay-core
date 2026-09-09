@extends('backend.layouts.app')
@section('title', __('Control Panel'))

@section('content')
<div class="container-fluid px-4">
    <!-- Header Section -->
    <div class="control-panel-header mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="header-content">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <x-icon name="apps-1" class="icon"/>
                    </div>
                    <div class="ms-3">
                        <h1 class="header-title mb-0">@lang('Control Panel')</h1>
                        <p class="header-subtitle mb-0">@lang('Quick access to all administrative features')</p>
                    </div>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-badge">
                    <span class="stat-number" id="total-features">{{ !empty($controlPanelData) ? count(array_merge(...array_column($controlPanelData, 'features'))) : 0 }}</span>
                    <span class="stat-label">@lang('Features')</span>
                </div>
            </div>
        </div>
        
        <!-- Live Search -->
        <div class="control-panel-search mt-3">
            <div class="search-wrapper">
                <div class="search-icon">
                    <x-icon name="search" class="icon"/>
                </div>
                <input type="text" id="control-panel-search" class="search-input" placeholder="@lang('Search features')..." autocomplete="off">
                <div class="search-clear d-none" id="search-clear">
                    <x-icon name="x" class="icon"/>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Panel Grid -->
    <div class="control-panel-grid">
        @foreach($controlPanelData as $sectionIndex => $section)
        <div class="section-block" data-section="{{ $sectionIndex }}">
            <div class="section-header">
                <div class="section-indicator"></div>
                <h2 class="section-title">{{ $section['label'] }}</h2>
                <span class="section-count">{{ count($section['features']) }}</span>
            </div>

            <div class="features-grid">
                @foreach($section['features'] as $featureIndex => $feature)
                <a href="{{ $feature['url'] }}" class="feature-card" data-color="{{ $feature['color'] }}" data-feature="{{ $featureIndex }}">
                    <div class="feature-content">
                        <div class="feature-header">
                            <div class="feature-icon-wrapper">
                                <div class="feature-icon bg-{{ $feature['color'] }}">
                                    <x-icon name="{{ $feature['icon'] }}" class="icon"/>
                                </div>
                            </div>
                            @if(isset($feature['parent']))
                            <span class="feature-parent">{{ $feature['parent'] }}</span>
                            @endif
                        </div>
                        
                        <div class="feature-body">
                            <h3 class="feature-title">{{ $feature['label'] }}</h3>
                            <p class="feature-description">{{ $feature['description'] }}</p>
                        </div>

                        <div class="feature-footer">
                            <span class="access-hint">@lang('Click to access')</span>
                            <div class="access-arrow">
                                <x-icon name="arrow-right" class="icon"/>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach

        @if(empty($controlPanelData))
        <div class="empty-state">
            <div class="empty-icon">
                <x-icon name="apps-1" class="icon"/>
            </div>
            <h3 class="empty-title">@lang('No features available')</h3>
            <p class="empty-description">@lang('Contact your administrator for access permissions.')</p>
        </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
    @include('backend.app.partials._control_panel_scripts')
@endpush
