<section class="feature-section fix section-padding pt-0">
    <div class="container">
        <div class="section-title text-center">
            <span class="sub-title justify-content-center wow fadeInUp">
                @if (!empty($data['title_bar_image']))
		            <img src="{{ asset($data['title_bar_image']) }}" alt="title-bar">
	            @endif
	            {{ $data['subheading'][$locale] ?? 'Features' }}
            </span>
	        
	        @if (!empty($data['heading'][$locale]))
		        <h2 class="wow fadeInUp" data-wow-delay=".3s">
			        {{ $data['heading'][$locale] }}
		        </h2>
	        @endif
        </div>
    </div>
	
	<div class="container-fluid">
        <div class="row">
	        @foreach($repeatedContents as $repeatedContent)
		        @php
			        $contentData = $repeatedContent->content_data ?? [];
		        @endphp
		        
		        <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
			        <div class="feature-box-item">
				        <div class="icon color-{{ $loop->index + 1 }}">
					        @if (!empty($contentData['feature_icon_class']))
						        <i class="{{ $contentData['feature_icon_class'] }}"></i>
					        @endif
				        </div>
				        
				        <div class="feature-content">
					        @if (!empty($contentData['feature_title'][$locale]))
						        <h3>{{ $contentData['feature_title'][$locale] }}</h3>
					        @endif
					        
					        @if (!empty($contentData['feature_text'][$locale]))
						        <p>{!! nl2br(e($contentData['feature_text'][$locale])) !!}</p>
					        @endif
				        </div>
			        </div>
		        </div>
	        @endforeach
        </div>
    </div>
</section>
