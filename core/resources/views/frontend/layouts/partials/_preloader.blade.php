@if (setting('site_preloader'))
    
    @php
		$preloaderTextString = setting('preloader_text');
		$preloaderTexts = explode(',', $preloaderTextString);
    @endphp
    
    <div id="preloader" class="preloader">
        <div class="animation-preloader">
            <div class="spinner">
            </div>
            <div class="txt-loading">
            @foreach($preloaderTexts as $text)
                <span data-text-preloader="{{ $text }}" class="letters-loading">
                    {{ $text }}
                </span>
            @endforeach
            </div>
            <p class="text-center">{{ __('Loading') }}</p>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
            </div>
        </div>
    </div>
@endif

