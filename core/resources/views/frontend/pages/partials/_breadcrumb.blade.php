
@php
    $imgUrl = $page->breadcrumb ? asset($page->breadcrumb) : asset(setting('default_breadcrumb_image'));
@endphp

<div class="breadcrumb-wrapper bg-cover" style="background-image: url({{$imgUrl}});">
	<div class="container">
		<div class="page-heading">
			<h1 class="wow fadeInUp" data-wow-delay=".3s">{{ $page->label }}</h1>
			<ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
				<li>
					<a href="{{  route('home') }}">
						{{ __('Home') }}
					</a>
				</li>
				<li>
					<i class="fas fa-chevron-right"></i>
				</li>
				<li>
					{{ $page->label }}
				</li>
			</ul>
		</div>
	</div>
</div>