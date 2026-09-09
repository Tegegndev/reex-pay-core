<section class="special-feature-section fix section-padding pt-0">
    <div class="container">
        <div class="section-title text-center">
            <span class="sub-title justify-content-center wow fadeInUp">
                @if (!empty($data['title_bar_image']))
		            <img src="{{ asset($data['title_bar_image']) }}" alt="img">
	            @endif
	            {{ $data['subheading'][$locale] ?? '' }}
            </span>
	        
	        @if (!empty($data['heading'][$locale]))
		        <h2 class="mb-2 wow fadeInUp" data-wow-delay=".3s">
			        {{ $data['heading'][$locale] }}
		        </h2>
	        @endif
	        
	        @if (!empty($data['description'][$locale]))
		        <p class="wow fadeInUp" data-wow-delay=".5s">
			        {!! nl2br(e($data['description'][$locale])) !!}
		        </p>
	        @endif
        </div>
	    
	    <div class="row g-4 align-items-center">
            <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".3s">
	            @foreach($repeatedContents->slice(0, 3) as $repeatedContent)
		            @php
			            $contentData = $repeatedContent->content_data ?? [];
		            @endphp
		            <div class="special-feature-item text-end">
			            @if (!empty($contentData['feature_icon']))
				            <div class="icon">
					            <img src="{{ asset($contentData['feature_icon']) }}" alt="icon">
				            </div>
			            @endif
			            <div class="content">
				            <h3>{{ $contentData['feature_title'][$locale] ?? '' }}</h3>
				            <p>{!! nl2br(e($contentData['feature_text'][$locale] ?? '')) !!}</p>
			            </div>
		            </div>
	            @endforeach
            </div>
		    
		    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".5s">
                <div class="special-feature-item mt-0">
	                @if (!empty($data['feature_center_image']))
		                <div class="feature-image">
			                <img src="{{ asset($data['feature_center_image']) }}" alt="feature-image">
		                </div>
	                @endif
                </div>
            </div>
		    
		    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay=".7s">
			    @foreach($repeatedContents->slice(3, 3) as $repeatedContent)
				    @php
					    $contentData = $repeatedContent->content_data ?? [];
				    @endphp
				    <div class="special-feature-item">
					    @if (!empty($contentData['feature_icon']))
						    <div class="icon">
							    <img src="{{ asset($contentData['feature_icon']) }}" alt="icon">
						    </div>
					    @endif
					    <div class="content">
						    <h3>{{ $contentData['feature_title'][$locale] ?? '' }}</h3>
						    <p>{!! nl2br(e($contentData['feature_text'][$locale] ?? '')) !!}</p>
					    </div>
				    </div>
			    @endforeach
            </div>
        </div>
    </div>
</section>
