<div class="modal fade" id="new-nav-modal" tabindex="-1" aria-labelledby="newNavModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content shadow-sm rounded-3">
			<div class="modal-header">
				<h5 class="modal-title" id="newNavModalLabel">{{ __('Add New Navigation') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<form action="{{ route('admin.navigation.site.store') }}" method="POST">
					@csrf
					
					{{-- Language Tabs --}}
					<ul class="nav nav-tabs mb-3" role="tablist">
						@foreach($locales as $locale => $label)
							<li class="nav-item" role="presentation">
								<button class="nav-link {{ $loop->first ? 'active' : '' }}"
								        id="nav-tab-{{ $locale }}"
								        data-coreui-toggle="tab"
								        data-coreui-target="#locale-{{ $locale }}"
								        type="button" role="tab">
									{{ $label }}
								</button>
							</li>
						@endforeach
					</ul>
					
					<div class="tab-content">
						@foreach($locales as $locale => $label)
							<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
							     id="locale-{{ $locale }}" role="tabpanel">
								<div class="mb-3">
									<label for="name_{{ $locale }}" class="form-label">{{ __('Name') }}
										<small class="text-muted text-uppercase">({{ $locale }})</small>
									</label>
									<input type="text" id="name_{{ $locale }}"
									       name="name[{{ $locale }}]"
									       class="form-control"
									       value="{{ old("name.$locale") }}">
								</div>
							</div>
						@endforeach
					</div>
					
					{{-- Label Row + Toggle --}}
					<div class="d-flex justify-content-between align-items-center mb-1">
						<label class="form-label mb-0" id="linked_label" for="page_id">{{ __('Linked Page') }}</label>
						
						<div class="form-check form-switch">
							<input class="form-check-input custom-url-toggle" type="checkbox" id="custom_url_toggle" name="custom_url" value="1"
								{{ old('custom_url') ? 'checked' : '' }}>
							<label class="form-check-label" for="custom_url_toggle">{{ __('Custom URL') }}</label>
						</div>
					</div>
					
					{{-- External URL input (when toggle is ON) --}}
					<div class="mb-3" id="slug_input_group" style="display: {{ old('custom_url') ? 'block' : 'none' }};">
						<input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="Enter external URL...">
					</div>
					
					{{-- Page selector (when toggle is OFF) --}}
					<div class="mb-3" id="page_select_group" style="display: {{ old('custom_url') ? 'none' : 'block' }};">
						<select class="form-select" id="page_id" name="page_id">
							<option value="">-- {{ __('Select Page') }} --</option>
							@foreach($pages as $page)
								<option value="{{ $page->id }}" {{ old('page_id') == $page->id ? 'selected' : '' }}>
									{{ $page->label }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="mb-3">
						<label for="target" class="form-label">{{ __('Link Target') }}</label>
						<select class="form-select" id="target" name="target">
							@foreach(\App\Enums\LinkTarget::options() as $value => $label)
								<option value="{{ $value }}" @selected(old('target') == $value)>
									{{ $label }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="col-lg-6 col-md-6 col-12">
						<label class="form-label" for="is_active">{{ __('Active Navigation') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" @checked(old('is_active',true )) type="checkbox" name="is_active" value="1">
						</div>
					</div>
					
					<div class="text-end">
						<button type="submit" class="btn btn-primary">
							<x-icon name="check" height="20" width="20"/>{{ __('Save Now') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
