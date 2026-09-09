<section class="cta-offer-section section-padding fix bg-cover" style="background-image: url('{{ asset($data['background_image']) }}');">
	<div class="container">
		<div class="section-title-area">
			<div class="section-title mb-0">
				@if (!empty($data['offer_title'][$locale]))
					<h2 class="wow fadeInUp" data-wow-delay=".3s">
						{{ $data['offer_title'][$locale] }}
					</h2>
				@endif
			</div>
			
			@if (!empty($data['button_text'][$locale]) && !empty($data['button_url']))
				<a href="{{ $data['button_url'] }}" class="theme-btn wow fadeInUp" data-wow-delay=".5s">
					<span>{{ $data['button_text'][$locale] }}</span>
				</a>
			@endif
		</div>
		<div class="counter-wrapper">
			@foreach($repeatedContents as $index => $repeatedContent)
				@php
					$contentData = $repeatedContent->content_data ?? [];
					$delay = 0.2 + ($index * 0.2);
				@endphp
				<div class="counter-item wow fadeInUp" data-wow-delay=".{{ intval($delay * 10) }}s">
					<h2>
						@if (!empty($contentData['counter_prefix']))
							{{ $contentData['counter_prefix'] }}
						@endif<span class="count">{{ $contentData['counter_number'] ?? '' }}</span>@if (!empty($contentData['counter_suffix']))
							{{ $contentData['counter_suffix'] }}
						@endif
					</h2>
					<p>{{ $contentData['counter_title'][$locale] ?? '' }}</p>
				</div>
			@endforeach
		</div>
	
	</div>
</section>
