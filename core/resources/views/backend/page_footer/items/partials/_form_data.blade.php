@php
	$isEdit = isset($footerItem);
	$idSuffix = $isEdit ? '_edit' : '';
@endphp

<form action="{{ $isEdit ? route('admin.page.footer.item.update', $footerItem->id) : route('admin.page.footer.item.store') }}"
      method="POST" id="{{ $isEdit ? 'edit-footer-item-data' : 'create-footer-item-form' }}">
	@csrf
	@if($isEdit)
		@method('PUT')
	@endif
	
	{{-- Section ID --}}
	<input type="hidden" name="section_id" value="{{ $isEdit ? $footerItem->footer_section_id : $footerSection->id }}">
	
	{{-- Label & Content Tabs --}}
	<ul class="nav nav-tabs mb-3">
		@foreach($locales as $locale => $label)
			<li class="nav-item">
				<button class="nav-link {{ $loop->first ? 'active' : '' }}"
				        data-coreui-toggle="tab"
				        data-coreui-target="#lang-{{ $locale }}{{ $idSuffix }}"
				        type="button">
					{{ $label }}
				</button>
			</li>
		@endforeach
	</ul>
	
	<div class="tab-content" id="footer-content-group{{ $idSuffix }}">
		@foreach($locales as $locale => $label)
			<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-{{ $locale }}{{ $idSuffix }}">
				{{-- Label --}}
				<div class="mb-3">
					<label class="form-label">{{ __('Label') }} ({{ strtoupper($locale) }})</label>
					<input type="text"
					       name="label[{{ $locale }}]"
					       class="form-control"
					       value="{{ old('label.'.$locale, $isEdit ? ($footerItem->label[$locale] ?? '') : '') }}">
				</div>
				
				{{-- Content --}}
				<div class="mb-3 content-textarea-group {{ $isEdit && $footerItem->url_type !== \App\Enums\FooterItemUrlType::NONE ? 'd-none' : '' }}">
					<label class="form-label">{{ __('Content Text') }} ({{ strtoupper($locale) }})</label>
					<textarea name="content[{{ $locale }}]"
					          class="form-control"
					          rows="4">{{ old('content.'.$locale, $isEdit ? ($footerItem->content[$locale] ?? '') : '') }}</textarea>
				</div>
			</div>
		@endforeach
	</div>
	
	{{-- URL Type --}}
	<div class="mb-3">
		<label class="form-label">{{ __('URL Type') }}</label>
		<select name="url_type"
		        class="form-select url-type-toggle"
		        id="url_type_selector{{ $idSuffix }}"
		        data-footer-id="{{ $idSuffix }}"
		        required>
			@foreach(\App\Enums\FooterItemUrlType::options() as $type)
				<option value="{{ $type->value }}"
					@selected(old('url_type', $isEdit ? $footerItem->url_type->value : '') == $type->value)>
					{{ $type->label() }}
				</option>
			@endforeach
		</select>
	</div>
	
	{{-- external URL --}}
	<div id="external-url-group{{ $idSuffix }}" class="mb-3 @if($isEdit && $footerItem->url_type !== \App\Enums\FooterItemUrlType::EXTERNAL_URL) d-none @endif">
		<label class="form-label">{{ __('External URL') }}</label>
		<input type="text" name="url" id="external_url_input{{ $idSuffix }}"
		       class="form-control"
		       value="{{ old('url', $isEdit ? $footerItem->url : '') }}"
		       placeholder="https://example.com">
	</div>
	
	{{-- Page Dropdown --}}
	<div id="page-dropdown-group{{ $idSuffix }}" class="mb-3 @if($isEdit && $footerItem->url_type !== \App\Enums\FooterItemUrlType::PAGE) d-none @endif">
		<label class="form-label">{{ __('Select Page') }}</label>
		<select name="page_id" class="form-select" id="page_id_selector{{ $idSuffix }}">
			<option value="">{{ __('-- Select Page --') }}</option>
			@foreach($pages as $page)
				<option value="{{ $page->id }}"
					@selected(old('page_id', $isEdit ? $footerItem->page_id : '') == $page->id)>
					{{ $page->label }}
				</option>
			@endforeach
		</select>
	</div>
	
	{{-- Social Dropdown --}}
	<div id="social-dropdown-group{{ $idSuffix }}" class="mb-3 @if($isEdit && $footerItem->url_type !== \App\Enums\FooterItemUrlType::SOCIAL) d-none @endif">
		<label class="form-label">{{ __('Select Social Platform') }}</label>
		<select name="social_id" class="form-select" id="social_id_selector{{ $idSuffix }}">
			<option value="">{{ __('-- Select Social Platform --') }}</option>
			@foreach($socials as $social)
				<option value="{{ $social->id }}" @selected(old('social_id', $isEdit ? $footerItem->social_id : '') == $social->id)>
					{{ $social->name }}
				</option>
			@endforeach
		</select>
	</div>
	
	<div class="mb-3">
		<label class="form-label">
			{{ __('Icon Class')  }} ({{ __('Optional') }})
			<a href="https://fontawesome.com/" target="_blank" class="badge bg-info text-white ms-2">{{ __('Font Awesome') }}</a>
		</label>
		<input type="text"
		       name="icon"
		       class="form-control"
		       value="{{ old('icon', $isEdit ? ($footerItem->icon ?? '') : '') }}">
	</div>
	
	{{-- Status --}}
	<div class="mb-3">
		<label class="form-check-label" for="status{{ $idSuffix }}">{{ __('Item Status') }}</label>
		<div class="form-check form-switch">
			<input class="form-check-input coevs-switch" type="checkbox" @checked(old('status', $isEdit ? $footerItem->status : true)) name="status" value="1" id="status{{ $idSuffix }}">
		</div>
	</div>
	
	{{-- Submit Button --}}
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" height="20" width="20"/>
			{{ $isEdit ? __('Update Now') : __('Save Now') }}
		</button>
	</div>

</form>
