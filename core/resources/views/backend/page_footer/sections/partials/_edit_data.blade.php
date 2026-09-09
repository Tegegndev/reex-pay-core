<form action="{{ route('admin.page.footer.section.update', $footerSection->id) }}" method="POST">
	@csrf
	@method('PUT')
	
	{{-- Language Tabs --}}
	<ul class="nav nav-tabs mb-3" role="tablist">
		@foreach($locales as $locale => $label)
			<li class="nav-item" role="presentation">
				<button class="nav-link {{ $loop->first ? 'active' : '' }}"
				        id="edit-title-tab-{{ $locale }}"
				        data-coreui-toggle="tab"
				        data-coreui-target="#edit-title-{{ $locale }}"
				        type="button" role="tab">
					{{ $label }}
				</button>
			</li>
		@endforeach
	</ul>
	
	<div class="tab-content">
		@foreach($locales as $locale => $label)
			<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
			     id="edit-title-{{ $locale }}" role="tabpanel">
				<div class="mb-3">
					<label for="edit_title_{{ $locale }}" class="form-label">
						{{ __('Section Title') }} <small class="text-muted">({{ strtoupper($locale) }})</small>
					</label>
					<input type="text"
					       name="title[{{ $locale }}]"
					       id="edit_title_{{ $locale }}"
					       class="form-control @error("title.$locale") is-invalid @enderror"
					       value="{{ old("title.$locale", $footerSection->title[$locale] ?? '') }}">
					@error("title.$locale")
					<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
			</div>
		@endforeach
	</div>
	
	{{-- Section Type --}}
	<div class="mb-3">
		<label class="form-label">{{ __('Section Type') }}</label>
		<select name="type" class="form-select" required>
			@foreach(\App\Enums\FooterSectionType::options() as $type)
				<option value="{{ $type->value }}"
					@selected(old('type', $footerSection->type->value) == $type->value)>
					{{ $type->label() }}
				</option>
			@endforeach
		</select>
		@error('type')
		<div class="invalid-feedback">{{ $message }}</div>
		@enderror
	</div>
	
	{{-- Section Status --}}
	<div class="mb-3">
		<label class="form-check-label" for="edit-section-status">{{ __('Section Status') }}</label>
		<div class="form-check form-switch">
			<input type="checkbox"
			       class="form-check-input coevs-switch"
			       id="edit-section-status"
			       name="status"
			       value="1"
				@checked(old('status', $footerSection->status))>
		</div>
	</div>
	
	{{-- Submit --}}
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" height="20" width="20"/>
			{{ __('Update Now') }}
		</button>
	</div>
</form>
