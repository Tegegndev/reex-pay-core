@extends('backend.layouts.app')
@section('title', __('Create Blog'))
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between mb-3">
			<h1 class="h4">{{ __('Create Blog') }}</h1>
			<a href="{{ route('admin.blog.post.index') }}" class="btn btn-primary">
				<x-icon name="back"/> {{ __('Back to Blog List') }}
			</a>
		</div>
		
		<div class="card border-0 mb-4">
			<div class="card-body">
				<form action="{{ route('admin.blog.post.store') }}" method="POST" enctype="multipart/form-data">
					@csrf
					
					{{-- Language Tabs --}}
					<ul class="nav nav-tabs mb-3" role="tablist">
						@foreach($locales as $locale => $label)
							<li class="nav-item" role="presentation">
								<button class="nav-link {{ $loop->first ? 'active' : '' }}"
								        id="create-lang-tab-{{ $locale }}"
								        data-coreui-toggle="tab"
								        data-coreui-target="#create-lang-{{ $locale }}"
								        type="button" role="tab">
									{{ $label }}
								</button>
							</li>
						@endforeach
					</ul>
					
					{{-- Tab Content --}}
					<div class="tab-content">
						@foreach($locales as $locale => $label)
							<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="create-lang-{{ $locale }}" role="tabpanel">
								{{-- Title --}}
								<div class="mb-3">
									<label class="form-label">{{ __('Title') }} ({{ strtoupper($locale) }})</label>
									<input type="text"
									       name="title[{{ $locale }}]"
									       value="{{ old('title.' . $locale) }}"
									       class="form-control {{ $locale == app()->getLocale() ? 'title-to-slug' : '' }}"
									       {{ $locale == app()->getLocale() ? 'data-slug-target=#blog-slug' : '' }}
									       placeholder="{{ __('Enter title') }}">
								</div>
								
								{{-- Excerpt --}}
								<div class="mb-3">
									<label class="form-label">{{ __('Excerpt') }} ({{ strtoupper($locale) }})</label>
									<textarea name="excerpt[{{ $locale }}]" class="form-control" rows="3">{{ old('excerpt.' . $locale) }}</textarea>
								</div>
								
								{{-- Content --}}
								<div class="mb-3">
									<label class="form-label">{{ __('Content') }} ({{ strtoupper($locale) }})</label>
									<textarea name="content[{{ $locale }}]" class="form-control summernote" rows="8">{{ old('content.' . $locale) }}</textarea>
								</div>
								
								{{-- SEO Meta Title --}}
								<div class="mb-3">
									<label class="form-label">{{ __('Meta Title') }} ({{ strtoupper($locale) }})</label>
									<input type="text"
									       name="meta_title[{{ $locale }}]"
									       value="{{ old('meta_title.' . $locale) }}"
									       class="form-control">
								</div>
								
								{{-- SEO Meta Description --}}
								<div class="mb-3">
									<label class="form-label">{{ __('Meta Description') }} ({{ strtoupper($locale) }})</label>
									<textarea name="meta_description[{{ $locale }}]" class="form-control" rows="2">{{ old('meta_description.' . $locale) }}</textarea>
								</div>
							
							</div>
						@endforeach
					</div>
					
					{{-- SEO Meta Keywords --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Meta Keywords') }}</label>
						<input type="text"
						       name="meta_keywords"
						       value="{{ old('meta_keywords') }}"
						       class="form-control tags-evs p-0">
					</div>
					
					{{-- Slug --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Slug (optional)') }}</label>
						<input type="text" name="slug" id="blog-slug" class="form-control" value="{{ old('slug') }}" placeholder="{{ __('Auto-generated or manually type') }}">
					</div>
					
					{{-- Category --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Category') }}</label>
						<select name="category_id" class="form-select">
							<option value="">{{ __('Select Category') }}</option>
							@foreach($categories as $category)
								<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
									{{ $category->name_text }}
								</option>
							@endforeach
						</select>
					</div>
					
					{{-- Thumbnail --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Thumbnail Image') }}</label>
						<x-img name="thumbnail"/>
					</div>
					
					{{-- Status --}}
					<div>
						<label class="form-check-label" for="blog-status">{{ __('Active') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" type="checkbox" name="status" id="blog-status" value="1" @checked(old('status', true))>
						</div>
					</div>
					
					
					{{-- Submit --}}
					<x-form.submit-button icon="check">
						{{ __('Create Blog') }}
					</x-form.submit-button>
				
				</form>
			</div>
		</div>
	</div>
@endsection
