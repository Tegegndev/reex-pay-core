<div class="modal fade" id="create-social-modal" tabindex="-1" aria-labelledby="createSocialModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content shadow-sm rounded-3">
			<div class="modal-header">
				<h5 class="modal-title" id="createSocialModalLabel">{{ __('Add New Social Link') }}</h5>
				<button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
			</div>
			
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.social.store') }}" id="create-social-form">
					@csrf
					
					{{-- Name --}}
					<div class="mb-3">
						<label for="social_name" class="form-label">{{ __('Name') }}</label>
						<input type="text" class="form-control" id="social_name" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter Social Name') }}" required>
					</div>
					
					{{-- Icon Class --}}
					<div class="mb-3">
						<label for="social_icon_class" class="form-label">
							{{ __('Icon Class') }}
							<a href="https://fontawesome.com/" target="_blank" class="badge bg-info text-white ms-2">{{ __('Font Awesome') }}</a>
						</label>
						<input type="text" class="form-control" id="social_icon_class" name="icon_class" value="{{ old('icon_class') }}" placeholder="{{ __('Enter Icon Class') }}" required>
					</div>
					
					
					{{-- Social URL --}}
					<div class="mb-3">
						<label for="social_url" class="form-label">{{ __('Social URL') }}</label>
						<input type="url" class="form-control" id="social_url" name="url" value="{{ old('url') }}" placeholder="{{ __('Enter Social URL') }}" required>
					</div>
					
					{{-- Link Target --}}
					<div class="mb-3">
						<label for="social_target" class="form-label">{{ __('Link Target') }}</label>
						<select class="form-select" name="target" id="social_target" required>
							@foreach(\App\Enums\LinkTarget::options() as $value => $label)
								<option value="{{ $value }}" @selected(old('target') == $value)>
									{{ $label }}
								</option>
							@endforeach
						</select>
					</div>
					
					{{-- Status --}}
					<div class="mb-3">
						<label class="form-check-label" for="social_status">{{ __('Active') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" @checked(old('status',true )) type="checkbox" name="status" value="1" id="social_status" checked>
						</div>
					</div>
					
					{{-- Submit --}}
					<div class="text-end">
						<button type="submit" class="btn btn-primary">
							<x-icon name="check" height="20" width="20"/>
							{{ __('Create Now') }}
						</button>
					</div>
				
				</form>
			</div>
		
		</div>
	</div>
</div>
