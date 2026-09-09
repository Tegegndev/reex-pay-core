<div class="header-top-section bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="container">
        <div class="header-top-wrapper">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-lg">REEXPAY LIMITED</span>
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Secure Payments</span>
            </div>
            <ul class="contact-list">
                <li>
                    <i class="far fa-envelope"></i>
                    <a href="mailto:{{ setting('support_email') }}" class="link text-white hover:text-indigo-200">{{ setting('support_email') }}</a>
                </li>
                <li>
                    <i class="fa-solid fa-phone-volume"></i>
                    <a href="tel:{{ setting('support_phone') }}" class="text-white hover:text-indigo-200">{{ setting('support_phone') }}</a>
                </li>
            </ul>
            <div class="top-right">

                @include('frontend.layouts.partials._language_switcher')
                @if($socials->isNotEmpty())
                    <div class="social-icon d-flex align-items-center">
                        <span class="text-white">{{ __('Follow Us') }}:</span>
                        @foreach($socials as $social)
                            <a href="{{ $social->url }}" target="{{ $social->target }}" class="text-white hover:text-indigo-200"><i class="{{ $social->icon_class }}"></i></a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
