@php
	$type = $meta['type'] ?? 'text';
	$class = $meta['class'] ?? 'col-md-12';
	$isTranslatable = ($meta['translatable'] ?? false) && $type !== 'img';
	$label = ucfirst(str_replace('_', ' ', $field));
	$fieldName = $isTranslatable ? "content_data[$field][$language]" : "content_data[$field]";
	$required = Str::contains($meta['validation'] ?? '', 'required');
 
	$ref = $required ? '' : 'coevs-remove-img';
@endphp

<div class="{{ $class }} mb-3">
	<label class="form-label">
		{{ $label }}
		@if($isTranslatable)
			<small class="text-muted text-uppercase">({{ $language }})</small>
		@endif
		@if($required)
			<span class="text-danger">*</span>
		@endif
	</label>
	
	@switch($type)
		@case('img')
			<x-img name="{{ $fieldName }}" :old="$value" :ref="$ref"/>
			@if(!empty($meta['recommended_size']))
				<div class="form-text">
					{{ __('Recommended size: :size pixels', ['size' => $meta['recommended_size']]) }}
				</div>
			@endif
			@break
		
		@case('textarea')
		@case('text_editor')
			<textarea name="{{ $fieldName }}" class="form-control @if($type === 'text_editor') summernote @endif">{{ $value }}</textarea>
			@break
		
		@default
			<input type="{{ $type }}" name="{{ $fieldName }}" class="form-control" value="{{ $value }}">
			@if(!empty($meta['info']))
				<div class="form-text">
					{{ $meta['info'] }}
				</div>
			@endif
	@endswitch
</div>
