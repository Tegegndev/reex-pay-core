<div class="modal fade" id="addContentModal" tabindex="-1" aria-labelledby="addContentModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addContentModalLabel">{{ __('Add Content') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="addContentForm" action="{{ route('admin.page.component-repeated-content.store') }}" method="POST" enctype="multipart/form-data">
				@csrf
				
				<input type="hidden" name="component_id" value="{{ $component->id }}">
				
				<div class="modal-body">
					@include('backend.page_component.partials._language_tabs_nav', ['languages' => $languages, 'tabIdPrefix' => 'modalLangTab'])
					<div class="tab-content rounded-bottom mt-3" id="modalLangTabContent">
						@foreach($languages as $code => $lang)
							@php
								$loopFirstRepeatData = $loop->first;
							@endphp
							<div class="tab-pane fade @if($loopFirstRepeatData) show active @endif"
							     id="modalLangTab-pane-{{ $code }}"
							     role="tabpanel"
							     aria-labelledby="modalLangTab-{{ $code }}">
								<div class="row">
									@foreach($repeatedFieldDefinitions as $field => $meta)
										@php
											$isTranslatable = ($meta['translatable'] ?? false) && (($meta['type'] ?? 'text') !== 'img');
											$oldValue = old("content_data.$field" . ($isTranslatable ? ".$code" : ''), '');
										@endphp
										@if($isTranslatable || $loopFirstRepeatData)
											@include('backend.page_component.partials._render_field', [
												'field'    => $field,
												'meta'     => $meta,
												'language' => $code,
												'value'    => $oldValue,
												'ref' 	=> 'addContentForm'
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
		</div>
	</div>
</div>