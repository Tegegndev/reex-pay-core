@php use App\Enums\KycStatus; @endphp
<!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    
    {{-- Head Include Here --}}
    @include('frontend.layouts.user.partials._head')
    
    <body>
    
    {{-- Header Include Here --}}
    @include('frontend.layouts.user.partials._navbar')
    
    {{-- Mobile Navbar Include Here --}}
    @include('frontend.layouts.user.partials._mobile_navbar')
    
    
    {{-- Main Area Here --}}
    <div id="mainArea" class="main-area mb-30">
        <div class="container">
            <div class="row wrapper fixed-wrapper">
                {{-- left bar --}}
                <div class="col-xl-3 col-lg-4 sidebar">
                    {{-- left bar wallet card --}}
                    @include('frontend.user.dashboard.partials._left_bar_card')
    
                    {{-- left bar menu --}}
                    @include('frontend.user.dashboard.partials._left_bar_menu')
                </div>
    
    
                <div class="col-xl-9 col-lg-8  main-content @if(!request()->routeIs('user.dashboard')) mt-neg-120 @endif">
                    {{-- kyc notice card --}}
                    @if(!auth()->user()->kycSubmission || auth()->user()->kycSubmission->status !== KycStatus::APPROVED)
                        @if(isActive('user.settings.kyc.verify') !== 'active')
                            @include('frontend.user.dashboard.partials._kyc_notice_card')
                        @endif
                    @endif
    
                    {{-- content area --}}
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    {{-- Footer Mobile Include here --}}
    @include('frontend.layouts.user.partials._footer_mobile')
    
    {{-- Scripts Include here --}}
    @include('frontend.layouts.user.partials._script')
    
    </body>
</html>