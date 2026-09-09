@extends('backend.layouts.app')

@section('title', __('Add Page Component'))

@section('content')
	<div class="clearfix mb-4">
		<h4 class="fw-semibold float-start">{{ __('Create Page Component') }}</h4>
		<a href="{{ route('admin.page.component.index') }}" class="btn btn-primary float-end">
			<x-icon name="back"/> {{ __('Back to List') }}
		</a>
	</div>
	
	<div class="card">
		<div class="card-body">
			<form action="{{ route('admin.page.component.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				
				{{-- Language Tabs --}}
				@include('backend.page_component.partials._language_tabs_nav', ['languages' => $languages, 'tabIdPrefix' => 'langTab'])
				
				
				<div class="tab-content rounded-bottom mt-3" id="langTabContent">
					@foreach($languages as $code => $lang)
						@php
							$loopFirst = $loop->first
						@endphp
						
						<div class="tab-pane fade @if($loopFirst) show active @endif"
						     id="langTab-pane-{{ $code }}"
						     role="tabpanel"
						     aria-labelledby="langTab-{{ $code }}">
							<div class="row">
								@if($loopFirst)
									{{-- Component Icon --}}
									<div class="mb-3 col-md-6">
										<label class="form-label">{{ __('Component Icon') }}</label>
										<x-img name="component_icon"/>
									</div>
									{{-- Component Name --}}
									<div class="mb-3 col-md-12">
										<label class="form-label">{{ __('Component Name') }}</label>
										<input type="text" name="component_name" class="form-control" value="{{ old('component_name') }}">
									</div>
								@endif
								
								@foreach($fieldDefinitions as $field => $meta)
									@php
										$isTranslatable = ($meta['translatable'] ?? false) && ($meta['type'] ?? 'text') !== 'img';
										$oldValue = old("content_data.$field" . ($isTranslatable ? ".$code" : ''), '');
									@endphp
									
									@if($isTranslatable || $loopFirst)
										@include('backend.page_component.partials._render_field', [
											'field'    => $field,
											'meta'     => $meta,
											'language' => $code,
											'value'    => $oldValue
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
						<input class="form-check-input coevs-switch" type="checkbox" name="is_active" value="1" @checked(old('is_active'))>
					</div>
				</div>
			
				{{-- Submit --}}
				<x-form.submit-button icon="check">
					{{ __('Create Component') }}
				</x-form.submit-button>
			</form>
		</div>
	</div>
@endsection
