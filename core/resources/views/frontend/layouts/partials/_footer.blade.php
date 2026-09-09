<section class="footer-section fix footer-bg">
    <div class="container">
        <div class="footer-widgets-wrapper">
            <div class="row justify-content-between">
                @foreach($footers as $index => $footer)
                    @php
                        // Calculate delay dynamically (start from 0s, then +0.02s each item)
						$delay = number_format($index * 0.02, 2) . 's';
                    @endphp
                    
                    <div class="{{ $footer->type->class() }} wow fadeInUp" data-wow-delay="{{ $delay }}">
                        <div class="single-footer-widget">
                            <div class="widget-head">
                                <h5>{{ $footer->title_text }}</h5>
                            </div>
                            @if($footer->type == \App\Enums\FooterSectionType::TEXT)
                                <div class="footer-content">
                                    @foreach($footer->items as $item)
                                        <h6 class="text-white mb-2">
                                            <i class="{{ $item->icon }}"></i>
                                            {{ $item->label_text }}
                                        </h6>
                                        <p class="mb-4">{{ $item->content_text }}</p>
                                    @endforeach
                                </div>
                            @else
                                <ul class="list-area">
                                    @foreach($footer->items as $item)
                                        <li>
                                            <a href="{{ $item->resolved_url }}" target="{{ $item->target }}">
                                                <i class="{{ $item->icon }}"></i>
                                                {{ $item->label_text }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endforeach
            
            </div>
        </div>
    </div>
    <div class="footer-bottom text-center">
        <p>{{ setting('copyright_text') }}</p>
    </div>
</section>
