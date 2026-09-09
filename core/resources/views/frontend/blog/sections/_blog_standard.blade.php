<section class="news-standard fix section-padding @unless($isBreadcrumb) mt-5 @endif ">
	<div class="container">
		<div class="news-details-area">
			<div class="row g-5">
				<div class="col-12 col-lg-8">
					@yield('blog_content')
				</div>
				
				<div class="col-12 col-lg-4">
					<div class="main-sidebar">
						
						{{-- Search Widget --}}
						<div class="single-sidebar-widget">
							<div class="wid-title">
								<h3>{{ __safe('Search') }}</h3>
							</div>
							<div class="search-widget">
								<form action="{{ route('blog.index') }}" method="GET">
									<input type="text" name="search" placeholder="Search here" value="{{ $searchQuery }}">
									<button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
								</form>
							</div>
						</div>
						
						{{-- Category List --}}
						<div class="single-sidebar-widget">
							<div class="wid-title">
								<h3>{{ __('Categories') }}</h3>
							</div>
							<div class="news-widget-categories">
								<ul>
									@foreach($categories as $category)
										<li class="{{ request('category') == $category->slug ? 'active' : '' }}">
											<a href="{{ route('blog.index', ['category' => $category->slug]) }}">
												{{ $category->name_text }}
											</a>
											<span>({{ $category->blogs_count ?? 0 }})</span>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
						
						{{-- Recent Posts --}}
						<div class="single-sidebar-widget">
							<div class="wid-title">
								<h3>{{ __('Recent Posts') }}</h3>
							</div>
							<div class="recent-post-area">
								@foreach($recentPosts as $recent)
									<div class="recent-items">
										<div class="recent-thumb">
											<img src="{{ asset($recent->thumbnail) }}" alt="recent-post-img">
										</div>
										<div class="recent-content">
											<ul>
												<li>
													<i class="fa-solid fa-calendar-days"></i>
													{{ $recent->created_at->format('d M, Y') }}
												</li>
											</ul>
											<h6>
												<a href="{{ route('blog.details', $recent->slug) }}">
													{{ Str::limit($recent->title_text, 50) }}
												</a>
											</h6>
										</div>
									</div>
								@endforeach
							</div>
						</div>
						
						{{-- Tags --}}
						<div class="single-sidebar-widget">
							<div class="wid-title">
								<h3>{{ __('Tags') }}</h3>
							</div>
							<div class="news-widget-categories">
								<div class="tagcloud">
									@foreach($allTags as $tag)
										<a href="{{ route('blog.index', ['tag' => Str::slug($tag)]) }}" class="{{ request('tag') == Str::slug($tag) ? 'active' : '' }}">
											{{ $tag }}
										</a>
									@endforeach
								</div>
							</div>
						</div>
					
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
