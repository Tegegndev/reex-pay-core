<header class="navbar-area d-lg-block d-none">
    <div class="container">
        <div class="navbar-wrap">
            <div class="row">
                <div class="col-xl-5 col-5 align-self-center">
                    <div class="logo-area d-flex align-items-center">
                        <a class="d-lg-inline-block d-none" href="{{ route('home') }}">
                            <img src="{{ asset(setting('logo')) }}" alt="REEXPAY">
                        </a>
                        <a class="d-lg-none d-inline-none" href="{{ route('home') }}">
                            <img src="{{ asset(setting('logo')) }}" alt="REEXPAY">
                        </a>
                        <h6 class="mb-0 d-lg-inline-block text-paragraph d-none">{{ __(':type Dashboard', ['type' => auth()->user()->role->title()]) }}</h6>
                    </div>
                </div>
                <div class="col-xl-7 col-7 text-end align-self-center">
                    <ul class="header-right align-self-center pb-0 mb-0">
                        <li>
                            @include('frontend.layouts.user.partials._language_switcher')
                        </li>

                        <li class="append-new-notification d-lg-inline-block d-none">
                            @include('frontend.layouts.user.partials._notifications')
                        </li>
                        <li class=" ms-3 position-relative mt-8" >
                            <button type="button"
                                    class="btn btn-light position-relative"
                                    id="quickFunctionBtnDesktop"
                                    title="Quick Function"
                            >
                                <x-icon name="apps" height="24" width="24" class="m-0"/>
                            </button>
                            @include('frontend.layouts.user.partials._quick_functions', ['dropdownId' => 'quickFunctionDropdownDesktop', 'btnId' => 'quickFunctionBtnDesktop'])
                        </li>
                        
                        @include('frontend.layouts.user.partials._author_card')
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>