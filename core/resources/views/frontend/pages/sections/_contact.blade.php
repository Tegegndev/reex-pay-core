<section class="contact-section section-padding pb-0" id="contact">
    <div class="container">
        <div class="contact-wrapper">
            <div class="section-title">
                <span class="sub-title wow fadeInUp">
                    @if (!empty($data['title_bar_image']))
		                <img src="{{ asset($data['title_bar_image']) }}" alt="title-bar">
	                @endif
	                {{ $data['subheading'][$locale] ?? '' }}
                </span>
	            
	            @if (!empty($data['heading'][$locale]))
		            <h2 class="wow fadeInUp" data-wow-delay=".3s">
			            {{ $data['heading'][$locale] }}
		            </h2>
	            @endif
            </div>
	        
	        <div class="contact-form-area">
                <div class="row g-4 justify-content-between align-items-center">
                    <div class="col-lg-6">
	                    <form action="{{ route('contact.submit') }}" method="POST" class="contact-form-items">
		                    @csrf
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-clt">
	                                    <span>{{ __('Your Name*') }}</span>
	                                    <input type="text" name="name" id="name" placeholder="{{ __('Robot Fox') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
	                                    <span>{{ __('Your Email*') }}</span>
	                                    <input type="email" name="email" id="email" placeholder="{{ __('info@example.com') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
	                                    <span>{{ __('Phone*') }}</span>
	                                    <input type="text" name="phone" id="phone" placeholder="{{ __('Phone') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-clt">
	                                    <span>{{ __('Subject*') }}</span>
	                                    <input type="text" name="subject" id="subject" placeholder="{{ __('Subject') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-clt">
	                                    <span>{{ __('Message*') }}</span>
	                                    <textarea name="message" id="message" placeholder="{{ __('Write Message') }}" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <button type="submit" class="theme-btn">
	                                    {{ __('Send Message') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
	                
	                <div class="col-lg-5">
                        <div class="contact-image">
	                        @if (!empty($data['contact_image']))
		                        <img src="{{ asset($data['contact_image']) }}" alt="contact-image">
	                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
