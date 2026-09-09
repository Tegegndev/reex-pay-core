<form action="{{ route('admin.page.component-repeated-content.update', $content->id) }}" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')
	<div class="modal-body">
		@include('backend.page_component.partials._language_tabs_nav', ['languages' => $locales, 'tabIdPrefix' => 'editModalLangTab'])
		<div class="tab-content rounded-bottom mt-3" id="editModalLangTabContent">
			@foreach($locales as $code => $lang)
				@php
					$loopFirstEditData = $loop->first;
				@endphp
				<div class="tab-pane fade @if($loopFirstEditData) show active @endif"
				     id="editModalLangTab-pane-{{ $code }}"
				     role="tabpanel"
				     aria-labelledby="editModalLangTab-{{ $code }}">
					<div class="row">
						@foreach($repeatedFieldDefinitions as $field => $meta)
							@php
								$isTranslatable = ($meta['translatable'] ?? false) && (($meta['type'] ?? 'text') !== 'img');
								$oldValue = old("content_data.$field" . ($isTranslatable ? ".$code" : ''), $content->content_data[$field][$code] ?? '');
                                if ($loopFirstEditData && !$isTranslatable) {
                                    $oldValue = old("content_data.$field", $content->content_data[$field] ?? '');
                                 }
							@endphp
							@if($isTranslatable || $loopFirstEditData)
								@include('backend.page_component.partials._render_field', [
									'field'    => $field,
									'meta'     => $meta,
									'language' => $code,
									'value'    => $oldValue ?? '',
								])
							@endif
						@endforeach
					</div>
				</div>
			@endforeach
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-warning text-white" data-coreui-dismiss="modal">
			<x-icon name="x" :height="20" :width="20"/>
			{{ __('Close') }}
		</button>
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" :height="20"/>
			{{ __('Save changes') }}
		</button>
	</div>
</form>