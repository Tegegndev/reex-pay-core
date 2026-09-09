<div class="modal fade" id="create-footer-section-modal" tabindex="-1" aria-labelledby="createFooterSectionLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content shadow-sm rounded-3">
			<div class="modal-header">
				<h5 class="modal-title">{{ __('Add Footer Section') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<form action="{{ route('admin.page.footer.section.store') }}" method="POST">
					@csrf
					
					{{-- Language Tabs --}}
					<ul class="nav nav-tabs mb-3" role="tablist">
						@foreach($locales as $locale => $label)
							<li class="nav-item" role="presentation">
								<button class="nav-link {{ $loop->first ? 'active' : '' }}"
								        id="title-tab-{{ $locale }}"
								        data-coreui-toggle="tab"
								        data-coreui-target="#title-{{ $locale }}"
								        type="button" role="tab">
									{{ $label }}
								</button>
							</li>
						@endforeach
					</ul>
					
					<div class="tab-content">
						@foreach($locales as $locale => $label)
							<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
							     id="title-{{ $locale }}" role="tabpanel">
								<div class="mb-3">
									<label for="title_{{ $locale }}" class="form-label">
										{{ __('Section Title') }} <small class="text-muted">({{ strtoupper($locale) }})</small>
									</label>
									<input type="text"
									       name="title[{{ $locale }}]"
									       class="form-control @error("title.$locale") is-invalid @enderror"
									       id="title_{{ $locale }}"
									       value="{{ old("title.$locale") }}">
								</div>
							</div>
						@endforeach
					</div>
					
					<div class="mb-3">
						<label class="form-label">{{ __('Section Type') }}</label>
						<select name="type" class="form-select" required>
							@foreach(\App\Enums\FooterSectionType::options() as $type)
								<option value="{{ $type->value }}" @selected(old('type') == $type->value)>
									{{ $type->label() }}
								</option>
							@endforeach
						</select>
					</div>
					
					
					<div class="mb-3">
						<label class="form-check-label" for="section-status">{{ __('Section Status') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" @checked(old('status',true )) type="checkbox" name="status" value="1" checked>
						</div>
					</div>
					
					<div class="text-end">
						<button type="submit" class="btn btn-primary">
							<x-icon name="check" height="20" width="20"/>
							{{ __('Save Now') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
