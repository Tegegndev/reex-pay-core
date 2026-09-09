@extends('backend.layouts.app')

@section('title', __('Page Component'))

@section('content')
	<div class="clearfix my-3">
		<div class="fs-4 fw-semibold float-start">{{ __('Edit Page Component') }}</div>
		<a href="{{ route('admin.page.component.index') }}" class="btn btn-primary float-end">
			<x-icon name="back"/> {{ __('Back to List') }}
		</a>
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body p-4">
			<form action="{{ route('admin.page.component.update', $component->id) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')
				
				{{-- Language Tabs Nav --}}
				@include('backend.page_component.partials._language_tabs_nav', ['languages' => $languages, 'tabIdPrefix' => 'componentLangTab'])
				
				<div class="tab-content mt-3" id="componentLangTabContent">
					@foreach($languages as $code => $lang)
						@php
							$loopFirst = $loop->first
						@endphp
						<div class="tab-pane fade @if($loopFirst) show active @endif"
						     id="componentLangTab-pane-{{ $code }}"
						     role="tabpanel"
						     aria-labelledby="componentLangTab-{{ $code }}">
							<div class="row">
								{{-- Basic fields rendered only on the first tab --}}
								@if($loopFirst)
									@if($component->type === \App\Enums\ComponentType::Dynamic)
										<div class="row">
											<div class="mb-3 col-md-6">
												<label class="form-label">{{ __('Component Icon') }}</label>
												<x-img name="component_icon" :old="$component->component_icon"/>
											</div>
										</div>
									@endif
									
									<div class="row">
										<div class="mb-3 col-md-6">
											<label class="form-label">{{ __('Component Name') }}</label>
											<input type="text" name="component_name" class="form-control" value="{{ old('component_name', $component->component_name) }}">
										</div>
									</div>
								@endif
								
								
								
								{{-- Render all custom fields --}}
								@foreach($fieldDefinitions as $field => $meta)
									@php
										$isTranslatable = ($meta['translatable'] ?? false) && (($meta['type'] ?? 'text') !== 'img');
										$value = $isTranslatable
											? old("content_data.$field.$code", $fieldValues[$field][$code] ?? '')
											: old("content_data.$field", $fieldValues[$field] ?? '');
									@endphp
									
									{{-- Render the field if it is translatable or if we are on the first language --}}
									@if($isTranslatable || $loopFirst)
										@include('backend.page_component.partials._render_field', [
											'field'    => $field,
											'meta'     => $meta,
											'language' => $code,
											'value'    => $value
										])
									@endif
								@endforeach
							</div>
						</div>
					@endforeach
				</div>
				
				
				{{-- Is Active Toggle --}}
				<div class="col-lg-6 col-md-6 col-12">
					<label class="form-label" for="status">{{ __('Is Active') }}</label>
					<div class="form-check form-switch">
						<input class="form-check-input coevs-switch" type="checkbox" name="is_active" value="1" @checked(old('is_active', $component->is_active))>
					</div>
				</div>
				
				
				
				{{-- Submit --}}
				<x-form.submit-button icon="check">
					{{ __('Save Changes') }}
				</x-form.submit-button>
			</form>
		</div>
	</div>
	
	{{-- Repeated content --}}
	@if($component->repeated_content)
		@include('backend.page_component.repeatable_content.index')
	@endif
@endsection
