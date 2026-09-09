@php
	$repeatedFieldDefinitions = App\Models\PageComponent::contentFields($componentKey, 'repeated_content');
	$repeatedTextFields = collect($repeatedFieldDefinitions)->where('type', 'text')->take(3);
@endphp

<div class="card border-0 mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0">{{ __('Repeatable Component Content') }}</h5>
		<button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#addContentModal">
			<x-icon name="plus"/> {{ __('Add Content') }}
		</button>
	</div>
	<div class="card-body p-4">

		@include('backend.page_component.repeatable_content.partials._notice_message')

		<div class="table-responsive">
			<table class="table user-table align-items-center">
				<thead class="table-light">
				<tr>
					@foreach($repeatedTextFields as $field => $meta)
						@php
							$isTranslatable = ($meta['translatable'] ?? false) && (($meta['type'] ?? 'text') !== 'img');
						@endphp
						<th>
							{{ ucfirst(str_replace('_', ' ', $field)) }}
							@if($isTranslatable)
								({{ array_key_first($languages) }})
							@endif
						</th>
					@endforeach
					<th> {{ __('Action') }} </th>
				</tr>
				
				</thead>
				<tbody>
				
				
				@forelse($component->repeatedContents as $content)
					<tr>
						@foreach($repeatedTextFields as $field => $meta)
							@php
								$isTrans = ($meta['translatable'] ?? false) && (($meta['type'] ?? 'text') !== 'img');
								$value = $isTrans
									? $content->content_data[$field][array_key_first($content->content_data[$field])] ?? ''
									: $content->content_data[$field] ?? '';
							@endphp
							<td>{{ $value }}</td>
						@endforeach
						
						<td>
							<button type="button" class="btn btn-sm btn-primary text-white edit-modal" data-edit-url="{{ route('admin.page.component-repeated-content.edit',$content->id) }}">
								<x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
							</button>
							<button type="button" class="btn btn-sm btn-danger text-white delete" data-url="{{ route('admin.page.component-repeated-content.destroy',$content->id) }}">
								<x-icon name="x" height="20" width="20"/> {{ __('Delete') }}
							</button>
						</td>
					
					</tr>
					
					
					{{-- Edit content modal --}}
					@include('backend.page_component.repeatable_content.modal.edit_content')
				
				@empty
					<tr>
						<td colspan="{{ count($repeatedTextFields) + 1 }}" class="text-center p-4">
							{{ __('No content found') }}
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>

{{-- Repeated content modal --}}
@include('backend.page_component.repeatable_content.modal.add_content')

@include('backend.page_component.repeatable_content.modal.edit_content')

@push('scripts')
	<script>
        editFormByModal('editContentModal', 'edit-content-data');
	</script>
@endpush