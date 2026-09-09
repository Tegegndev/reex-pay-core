<form action="{{ route('admin.navigation.site.update', $navigation->id) }}" method="POST">
	@csrf
	@method('PUT')
	
	{{-- Language Tabs --}}
	<ul class="nav nav-tabs mb-3" role="tablist">
		@foreach($locales as $locale => $label)
			<li class="nav-item" role="presentation">
				<button class="nav-link {{ $loop->first ? 'active' : '' }}"
				        id="edit-tab-{{ $locale }}-{{ $navigation->id }}"
				        data-coreui-toggle="tab"
				        data-coreui-target="#edit-locale-{{ $locale }}-{{ $navigation->id }}"
				        type="button" role="tab">
					{{ $label }}
				</button>
			</li>
		@endforeach
	</ul>
	
	<div class="tab-content">
		@foreach($locales as $locale => $label)
			<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
			     id="edit-locale-{{ $locale }}-{{ $navigation->id }}" role="tabpanel">
				<div class="mb-3">
					<label for="name_{{ $locale }}_{{ $navigation->id }}" class="form-label">
						{{ __('Name') }}
						<small class="text-muted text-uppercase">({{ $locale }})</small>
					</label>
					<input type="text"
					       id="name_{{ $locale }}_{{ $navigation->id }}"
					       name="name[{{ $locale }}]"
					       class="form-control"
					       value="{{ old("name.$locale", $navigation->name[$locale] ?? '') }}">
				</div>
			</div>
		@endforeach
	</div>
	
	{{-- Label + Toggle --}}
	<div class="d-flex justify-content-between align-items-center mb-1">
		<label class="form-label mb-0" id="linked_label_{{ $navigation->id }}">
			{{ $navigation->is_custom_url ? __('External URL') : __('Linked Page') }}
		</label>
		<div class="form-check form-switch">
			<input class="form-check-input custom-url-toggle"
			       type="checkbox"
			       name="custom_url"
			       value="1"
			       id="custom_url_toggle_{{ $navigation->id }}"
			       data-nav-id="_{{ $navigation->id }}"
				{{ $navigation->is_custom_url ? 'checked' : '' }}>
			<label class="form-check-label ms-2" for="custom_url_toggle_{{ $navigation->id }}">{{ __('Custom URL') }}</label>
		</div>
	</div>
	
	{{-- Slug input --}}
	<div class="mb-3" id="slug_input_group_{{ $navigation->id }}" style="display: {{ $navigation->is_custom_url ? 'block' : 'none' }};">
		<input type="text" id="slug_{{ $navigation->id }}" name="slug"
		       class="form-control"
		       value="{{ old('slug', $navigation->slug) }}"
		       placeholder="Enter external URL...">
	</div>
	
	{{-- Page select --}}
	<div class="mb-3" id="page_select_group_{{ $navigation->id }}" style="display: {{ $navigation->is_custom_url ? 'none' : 'block' }};">
		<select class="form-select" id="page_id_{{ $navigation->id }}" name="page_id">
			<option value="">-- {{ __('Select Page') }} --</option>
			@foreach($pages as $page)
				<option value="{{ $page->id }}" {{ old('page_id', $navigation->page_id) == $page->id ? 'selected' : '' }}>
					{{ $page->label }}
				</option>
			@endforeach
		</select>
	</div>
	
	{{-- Target --}}
	<div class="mb-3">
		<label for="target_{{ $navigation->id }}" class="form-label">{{ __('Link Target') }}</label>
		<select class="form-select" id="target_{{ $navigation->id }}" name="target">
			@foreach(\App\Enums\LinkTarget::options() as $value => $label)
				<option value="{{ $value }}" @selected(old('target', $navigation->target) == $value)>
					{{ $label }}
				</option>
			@endforeach
		</select>
	</div>
	
	{{-- Active Switch --}}
	<div class="col-lg-6 col-md-6 col-12 mb-3">
		<label class="form-label" for="is_active_{{ $navigation->id }}">{{ __('Active Navigation') }}</label>
		<div class="form-check form-switch">
			<input class="form-check-input coevs-switch" type="checkbox" name="is_active"
			       id="is_active_{{ $navigation->id }}"
			       value="1"
				{{ $navigation->is_active ? 'checked' : '' }}>
		</div>
	</div>
	
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" class="me-1" height="20" width="20"/>
			{{ __('Update Now') }}
		</button>
	</div>
</form>
