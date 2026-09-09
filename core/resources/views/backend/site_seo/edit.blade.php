@extends('backend.layouts.app')

@section('title', __('Manage SEO'))

@section('content')
	@php
		$metaKeywords = old('meta_keywords');
		if (blank($metaKeywords) && !blank($seo->meta_keywords)) {
			$metaKeywords = json_encode(
				collect(explode(',', $seo->meta_keywords))
					->filter()
					->map(fn ($keyword) => ['value' => $keyword])
					->values()
					->toArray()
			);
		}
	@endphp
	<div class="py-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1 class="h4">{{ __(':name SEO Manage', ['name' => $seo->isGlobal() ? __('Global') : $seo->page->label]) }}</h1>
			<a href="{{ route('admin.site-seo.index') }}" class="btn btn-primary">
				<x-icon name="back" height="20" width="20"/>
				{{ __('Back to SEO Management') }}
			</a>
		</div>
		
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<form action="{{ route('admin.site-seo.update', $seo->id) }}" method="POST" enctype="multipart/form-data">
					@csrf
					@method('PUT')
					
					{{-- Page Dropdown --}}
					@unless($seo->isGlobal())
						<div class="mb-3">
							<label class="form-label">{{ __('Select Page (optional)') }}</label>
							<select name="page_id" class="form-select">
								<option value="">{{ __('-- Select a page --') }}</option>
								@foreach($pages as $page)
									<option value="{{ $page->id }}" @selected(old('page_id', $seo->page_id) == $page->id)>
										{{ $page->label }}
									</option>
								@endforeach
							</select>
						</div>
					@endunless
					
					{{-- Language Tabs --}}
					{{-- Note: The language tabs are generated dynamically based on the available locales --}}
					{{-- Language Tabs --}}
					<ul class="nav nav-tabs mb-3" role="tablist">
						@foreach($locales as $locale => $label)
							<li class="nav-item" role="presentation">
								<button class="nav-link {{ $loop->first ? 'active' : '' }}"
								        id="edit-lang-tab-{{ $locale }}"
								        data-coreui-toggle="tab"
								        data-coreui-target="#edit-lang-{{ $locale }}"
								        type="button" role="tab">
									{{ $label }}
								</button>
							</li>
						@endforeach
					</ul>
					
					{{-- Tab Content --}}
					<div class="tab-content">
						@foreach($locales as $locale => $label)
							<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="edit-lang-{{ $locale }}" role="tabpanel">
								<div class="mb-3">
									<label class="form-label">{{ __('Meta Title') }} ({{ strtoupper($locale) }})</label>
									<input type="text" name="meta_title[{{ $locale }}]" class="form-control"
									       value="{{ old('meta_title.' . $locale, $seo->meta_title[$locale] ?? '') }}">
								</div>
								
								<div class="mb-3">
									<label class="form-label">{{ __('Meta Description') }} ({{ strtoupper($locale) }})</label>
									<textarea name="meta_description[{{ $locale }}]" class="form-control"
									          rows="2">{{ old('meta_description.' . $locale, $seo->meta_description[$locale] ?? '') }}</textarea>
								</div>
							</div>
						@endforeach
					</div>
					
					{{-- Meta Keywords --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Meta Keywords') }}</label>
						<input type="text" name="meta_keywords" class="form-control tags-evs p-0" value="{{$metaKeywords  }}">
					
					</div>
					
					{{-- Canonical URL --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Canonical URL') }}</label>
						<input type="url" name="canonical_url" class="form-control"
						       placeholder="{{ __('Example: https://example.com/page') }}"
						       value="{{ old('canonical_url', $seo->canonical_url) }}">
					</div>
					
					{{-- Robots --}}
					<div class="mb-3">
						<label class="form-label">{{ __('Robots Directive') }}</label>
						<select name="robots" class="form-select">
							<option value="index,follow" @selected(old('robots', $seo->robots) == 'index,follow')>Index, Follow</option>
							<option value="noindex,nofollow" @selected(old('robots', $seo->robots) == 'noindex,nofollow')>No Index, No Follow</option>
						</select>
					</div>
					
					{{-- SEO Image --}}
					<div class="mb-3">
						<label class="form-label">{{ __('SEO Image (Optional)') }}</label>
						<x-img name="image" :old="$seo->image"/>
						<div class="form-text">
							{{ __('Recommended size: 1200x630 pixels') }}
						</div>
					</div>
					
					{{-- Submit --}}
					<x-form.submit-button icon="check">
						{{ __('Save Changes') }}
					</x-form.submit-button>
				
				</form>
			</div>
		</div>
	</div>
@endsection
