@extends('frontend.blog.index')
@section('blog_content')
	<section class="news-section fix">
		<div class="container">
			<div class="row g-4">
				@if($blogs->count())
					
					@foreach($blogs as $blog)
						<div class="col-xl-12 wow fadeInUp" data-wow-delay=".3s">
							<div class="news-card-items style-2 mt-0 pb-0 h-100 d-flex flex-column">
								<!-- Blog Image -->
								<div class="news-image blog-list-img position-relative overflow-hidden">
									<img src="{{ asset($blog->thumbnail) }}" alt="{{ $blog->title_text }}" class="img-fluid w-100">
									<div class="post-date">
										<h3 class="mb-0">
											{{ $blog->created_at->format('d') }}
											<br>
											<span>{{ $blog->created_at->format('M') }}</span>
										</h3>
									</div>
								</div>
								
								<!-- Blog Content -->
								<div class="news-content flex-grow-1 d-flex flex-column justify-content-between">
									<div class="content-top">
										<ul class="list-unstyled d-flex align-items-center gap-3 mb-3 small text-muted">
											<li>
												<i class="fa-solid fa-tag me-1 text-primary"></i>
												{{ $blog->category->name_text ?? 'Uncategorized' }}
											</li>
											<li>
												<i class="fa-solid fa-user"></i> {{ __('by') }} {{ $blogs[0]->author->name ?? 'Admin ' }}
											</li>
										</ul>
										
										<h3 class="fs-5 fw-bold mb-2">
											<a href="{{ route('blog.details', $blog->slug) }}" class="text-dark text-decoration-none hover-theme">
												{{ Str::limit($blog->title_text, 70) }}
											</a>
										</h3>
										
										<p class="mb-0">
											{{ Str::limit($blog->excerpt_text, 200) }}
										</p>
									</div>
									
									<div class="mt-3">
										<a href="{{ route('blog.details', $blog->slug) }}" class="theme-btn-2 d-inline-flex align-items-center gap-2">
											{{ __('Read More') }}
											<i class="fa-solid fa-arrow-right"></i>
										</a>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				
				@else
					<!-- No Blog Found -->
					<div class="col-12 text-center py-5">
						<h4 class="text-muted">{{ __('No blog posts found.') }}</h4>
						<p class="small text-muted">{{ __('Please try adjusting your search or browse other categories.') }}</p>
					</div>
				@endif
			</div>
			
			
			<!-- Pagination -->
			@if($blogs->hasPages())
				<div class="page-nav-wrap pt-5 text-center wow fadeInUp" data-wow-delay=".3s">
					{{ $blogs->links() }}
				</div>
			@endif
		</div>
	</section>
@endsection
