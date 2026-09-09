<section class="work-process-section fix section-padding">
    <div class="container">
        <div class="section-title text-center">
            <span class="sub-title justify-content-center wow fadeInUp">
                @if (!empty($data['title_bar_image']))
		            <img src="{{ asset($data['title_bar_image']) }}" alt="title-bar">
	            @endif
	            {{ $data['subheading'][$locale] ?? '' }}
            </span>
	        
	        @if (!empty($data['heading'][$locale]))
		        <h2 class="wow fadeInUp" data-wow-delay=".3s">{{ $data['heading'][$locale] }}</h2>
	        @endif
        </div>
	    
	    <div class="process-work-wrapper">
		    @if (!empty($data['line_shape_image']))
			    <div class="line-shape">
				    <img src="{{ asset($data['line_shape_image']) }}" alt="line-shape">
			    </div>
		    @endif
            
            <div class="row">
	            @foreach($repeatedContents as  $repeatedContent)
		            @php
			            $index = $loop->index;
						$contentData = $repeatedContent->content_data ?? [];
						$delay = 0.3 + ($index * 0.2);
						$isEven = ($index + 1) % 2 === 0;
		            @endphp
		            
		            <div class="col-xl-3 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".{{ intval($delay * 10) }}s">
			            <div class="work-process-items text-center">
				            {{-- Icon Part --}}
				            @if($isEven)
					            <div class="content style-2">
						            @if (!empty($contentData['step_title'][$locale]))
							            <h5>{{ $contentData['step_title'][$locale] }}</h5>
						            @endif
						            @if (!empty($contentData['step_description'][$locale]))
							            <p>{!! nl2br(e($contentData['step_description'][$locale])) !!}</p>
						            @endif
					            </div>
					            <div class="icon bg-{{ $index + 1 }}">
						            @if (!empty($contentData['step_icon_class']))
							            <i class="{{ $contentData['step_icon_class'] }}"></i>
						            @endif
						            <h6 class="number">{{ $index + 1 }}</h6>
					            </div>
				            @else
					            <div class="icon {{ !$isEven ? '' : 'bg-' . ($index + 1) }}">
						            @if (!empty($contentData['step_icon_class']))
							            <i class="{{ $contentData['step_icon_class'] }}"></i>
						            @endif
						            <h6 class="number">{{ $index + 1 }}</h6>
					            </div>
					            <div class="content">
						            @if (!empty($contentData['step_title'][$locale]))
							            <h5>{{ $contentData['step_title'][$locale] }}</h5>
						            @endif
						            @if (!empty($contentData['step_description'][$locale]))
							            <p>{!! nl2br(e($contentData['step_description'][$locale])) !!}</p>
						            @endif
					            </div>
				            @endif
			            </div>
		            </div>
	            @endforeach
            </div>
        </div>
    </div>
</section>
