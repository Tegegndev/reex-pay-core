@php
	$detailsTags = collect(explode(',', $blogDetails->meta_keywords ?? ''))
    ->map(fn($tag) => trim($tag, " \t\n\r\0\x0B\""))
    ->filter()
    ->unique();
    $currentUrl = urlencode(request()->fullUrl());
    $shareTitle = urlencode($blogDetails->title_text ?? config('app.name'));
@endphp
@extends('frontend.blog.index')
@section('blog_content')
	<div class="blog-post-details">
		<div class="single-blog-post">
			
			<!-- Blog Featured Image -->
			<div class="post-featured-thumb bg-cover" style="background-image: url('{{ asset($blogDetails->thumbnail) }}');"></div>
			
			<!-- Blog Content -->
			<div class="post-content">
				
				<!-- Post Meta -->
				<ul class="post-list d-flex align-items-center">
					<li>
						<i class="fa-solid fa-calendar-days"></i>
						{{ $blogDetails->created_at->format('d M, Y') }}
					</li>
					<li>
						<i class="fa-solid fa-tag"></i>
						{{ $blogDetails->category->name_text ?? __('Uncategorized') }}
					</li>
					<li>
						<i class="fa-solid fa-user"></i> {{ __('by') }} {{ $blogs[0]->author->name ?? 'Admin ' }}
					</li>
				</ul>
				
				<!-- Blog Title -->
				<h3>{{ $blogDetails->title_text }}</h3>
				
				<!-- Blog Body Content (summernote rendered) -->
				<div class="blog-body-content mt-4">
					{!! $blogDetails->content[app()->getLocale()] ?? '' !!}
				</div>
				
				<!-- Tags & Social Share -->
				<div class="row tag-share-wrap mt-4 mb-5">
					<div class="col-lg-8 col-12">
						<div class="tagcloud">
							@foreach($detailsTags as $tag)
								<a href="{{ route('blog.index', ['tag' => Str::slug($tag)]) }}" class="mt-2 rounded">
									{{ $tag }}
								</a>
							@endforeach
						</div>
					</div>
					<div class="col-lg-4 col-12 mt-3 mt-lg-0 text-lg-end">
						<div class="social-share">
							<span class="me-3">{{ __('Share') }}:</span>
							<!-- Facebook -->
							<a href="https://www.facebook.com/sharer/sharer.php?u={{ $currentUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none me-2">
								<i class="fab fa-facebook-f"></i>
							</a>
							
							<!-- Twitter -->
							<a href="https://twitter.com/intent/tweet?url={{ $currentUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none me-2">
								<i class="fab fa-twitter"></i>
							</a>
							
							<!-- LinkedIn -->
							<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $currentUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
								<i class="fab fa-linkedin-in"></i>
							</a>
						</div>
					</div>
				</div>
			
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script type="application/ld+json">
		{
		  "@context": "https://schema.org",
		  "@type": "BlogPosting",
		  "headline": "{{ $blogDetails->title_text }}",
		  "datePublished": "{{ $blogDetails->created_at->toAtomString() }}",
		  "author": {
		    "@type": "Person",
		    "name": "{{ $blogDetails->author->name ?? 'Admin' }}"
		  },
		  "image": "{{ asset($blogDetails->thumbnail) }}",
		  "publisher": {
		    "@type": "Organization",
		    "name": "{{ config('app.name') }}"
		  }
		}
	</script>
@endsection