@php
	use App\Services\PageMetaService;
	use App\Models\BlogCategory;
	use App\Models\Blog;

	$page = App\Models\Page::blog();
    
    $isBlogDetails = $blogDetails ?? false;
    
	$meta = PageMetaService::build($page, $blogDetails ?? null);
	$isBreadcrumb = $page->is_breadcrumb;

	$searchQuery = request('search');
	$categories = BlogCategory::activeCached();
	$recentPosts = Blog::activeCached()->take(3);

	  // Fetch all meta_keywords from blogs
	$allTags = Blog::activeCached()
				->pluck('meta_keywords')
				->flatten()
				->filter()
				->flatMap(function ($keywords) {
					return collect(explode(',', $keywords));
				})
				->map(fn($tag) => trim($tag))
				->filter()
				->unique()
				->take(20);
@endphp
@extends('frontend.layouts.app')
@section('content')
	@if($isBreadcrumb)
		@include('frontend.pages.partials._breadcrumb')
	@endif
	
	@include('frontend.blog.sections._'.$page->components->first()->section_name)

@endsection
