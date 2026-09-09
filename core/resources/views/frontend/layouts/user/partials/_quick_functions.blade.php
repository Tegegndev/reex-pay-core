<!-- Quick Function Dropdown Popup -->
<div id="{{ $dropdownId ?? 'quickFunctionDropdown' }}" class="quick-function-dropdown shadow-lg" role="menu" aria-label="{{ __('Quick Functions') }}">
    <!-- Header -->
    <div class="qf-header d-flex align-items-center justify-content-between mb-2 mt-1 px-3 py-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('user.dashboard') }}" class="qf-header-icon d-flex align-items-center justify-content-center text-decoration-none"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="{{ __('Dashboard') }}">
                <x-icon name="dashboard" height="25" width="25" class="text-primary"/>
            </a>
            <span class="fw-bold fs-5">{{ __('Quick Functions') }}</span>
        </div>
        <button type="button" class="btn-close btn-sm" aria-label="{{ __('Close') }}"
                onclick="document.getElementById('{{ $dropdownId ?? 'quickFunctionDropdown' }}').classList.remove('show')"></button>
    </div>
    <!-- Main grid -->
    <div class="quick-grid px-3 mb-2" id="quickFunctionMainGrid_{{ $dropdownId ?? 'default' }}">
        @foreach($quickLinksMain as $link)
            <a href="{{ $link['link'] }}" class="quick-action" title="{{ $link['title'] }}" role="menuitem" tabindex="0">
                <span class="quick-icon">
                    <x-icon name="{{ $link['icon'] }}" class="text-primary"  height="28" width="28"/>
                </span>
                <span>{{ $link['title'] }}</span>
            </a>
        @endforeach
    </div>
    <!-- More Functions grid -->
    <div class="quick-grid px-3 mb-2 qf-more-grid" id="quickFunctionMoreGrid_{{ $dropdownId ?? 'default' }}">
        @foreach($quickLinksMore as $link)
            <a href="{{ $link['link'] }}" class="quick-action" title="{{ $link['title'] }}" role="menuitem" tabindex="0">
                <span class="quick-icon">
                    <x-icon name="{{ $link['icon'] }}"  height="28" width="28"/>
                </span>
                <span>{{ $link['title'] }}</span>
            </a>
        @endforeach
    </div>
    <!-- Footer -->
    <div class="qf-footer px-3 pb-2 pt-2 d-flex align-items-center justify-content-center">
        <button id="qfMoreBtn_{{ $dropdownId ?? 'default' }}" class="more-functions btn btn-sm btn-primary rounded-pill  d-flex align-items-center gap-2"
                aria-expanded="false" aria-controls="quickFunctionMoreGrid_{{ $dropdownId ?? 'default' }}">
            <span id="qfMoreBtnLabel_{{ $dropdownId ?? 'default' }}"><i class="fa-solid fa-chevron-down me-1"></i> {{ __('More Functions') }}</span>
        </button>
    </div>
</div>
@push('scripts')
    @include('frontend.layouts.user.partials._quick_functions_js')
@endpush