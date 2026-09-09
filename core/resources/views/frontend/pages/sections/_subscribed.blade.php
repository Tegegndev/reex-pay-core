<section class="cta-subscribed-section fix">
    <div class="container">
        <div class="subscribed-wrapper">
            <div class="sub-shape">
	            @if (!empty($data['email_image']))
		            <img src="{{ asset($data['email_image']) }}" alt="email-shape">
	            @endif
            </div>
            <div class="dot-shape">
	            @if (!empty($data['dot_shape_image']))
		            <img src="{{ asset($data['dot_shape_image']) }}" alt="dot-shape">
	            @endif
            </div>
            <div class="subscribed-content text-center">
	            <h5>{{ $data['small_title'][$locale] ?? '' }}</h5>
	            <h2>{{ $data['heading'][$locale] ?? '' }}</h2>
            </div>
	        <form action="{{ route('subscribe.submit') }}" id="contact-forms" method="POST" class="contact-form-items">
		        @csrf
                <div class="newsletter-form">
	                <input type="email" name="email" id="email2" placeholder="Enter your email address">
	                <input type="submit" value="{{ $data['button_text'][$locale] ?? '' }}">
                </div>
            </form>
        </div>
    </div>
</section>
