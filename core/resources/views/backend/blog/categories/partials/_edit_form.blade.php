<form action="{{ route('admin.blog.category.update', $blogCategory->id) }}" method="POST" id="edit-category-form">
	@csrf
	@method('PUT')
	{{-- Language Tabs --}}
	<ul class="nav nav-tabs mb-3">
		@foreach($locales as $locale => $label)
			<li class="nav-item">
				<button class="nav-link {{ $loop->first ? 'active' : '' }}"
				        data-coreui-toggle="tab"
				        data-coreui-target="#edit-locale-{{ $locale }}" type="button">
					{{ $label }}
				</button>
			</li>
		@endforeach
	</ul>
	
	<div class="tab-content">
		@foreach($locales as $locale => $label)
			<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="edit-locale-{{ $locale }}">
				<div class="mb-3">
					<label class="form-label">{{ __('Name') }} ({{ strtoupper($locale) }})</label>
					<input type="text"
					       name="name[{{ $locale }}]"
					       class="form-control {{ $locale == config('app.default_language') ? 'title-to-slug' : '' }}"
					       data-slug-target="#slug_field_id"
					       value="{{ old("name.$locale", $blogCategory->name[$locale] ?? '') }}">
				
				</div>
			</div>
		@endforeach
	</div>
	
	<div class="mb-3">
		<label class="form-label">{{ __('Slug') }}</label>
		<input type="text" name="slug" id="slug_field_id" class="form-control" value="{{ old('slug', $blogCategory->slug) }}">
	
	</div>
	
	<div class="mb-3">
		<label class="form-check-label" for="edit-category-status">{{ __('Active') }}</label>
		<div class="form-check form-switch">
			<input class="form-check-input coevs-switch" type="checkbox" name="status" id="edit-category-status" value="1" @checked($blogCategory->status)>
		</div>
	</div>
	
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" height="20" width="20"/>
			{{ __('Update Now') }}
		</button>
	</div>

</form>
