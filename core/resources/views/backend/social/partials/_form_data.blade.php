<form method="POST" action="{{ route('admin.social.update', $social->id) }}" id="edit-social-form">
	@csrf
	@method('PUT')
	
	{{-- Name --}}
	<div class="mb-3">
		<label for="edit_social_name" class="form-label">{{ __('Name') }}</label>
		<input type="text" class="form-control" id="edit_social_name" name="name" value="{{ old('name', $social->name) }}" placeholder="{{ __('Enter Social Name') }}" required>
	</div>
	
	{{-- Icon Class --}}
	<div class="mb-3">
		<label for="edit_social_icon_class" class="form-label">
			{{ __('Icon Class') }}
			<a href="https://fontawesome.com/" target="_blank" class="badge bg-info text-white ms-2">{{ __('Font Awesome') }}</a>
		</label>
		<input type="text" class="form-control" id="edit_social_icon_class" name="icon_class" value="{{ old('icon_class', $social->icon_class) }}" placeholder="{{ __('Enter Icon Class') }}" required>
	</div>
	
	
	{{-- Social URL --}}
	<div class="mb-3">
		<label for="edit_social_url" class="form-label">{{ __('Social URL') }}</label>
		<input type="url" class="form-control" id="edit_social_url" name="url" value="{{ old('url', $social->url) }}" placeholder="{{ __('Enter Social URL') }}" required>
	</div>
	
	{{-- Link Target --}}
	<div class="mb-3">
		<label for="edit_social_target" class="form-label">{{ __('Link Target') }}</label>
		<select class="form-select" name="target" id="edit_social_target" required>
			@foreach(\App\Enums\LinkTarget::options() as $value => $label)
				<option value="{{ $value }}" @selected(old('target', $social->target->value) == $value)>
					{{ $label }}
				</option>
			@endforeach
		</select>
	</div>
	
	{{-- Status --}}
	<div class="mb-3">
		<label class="form-check-label" for="edit_social_status">{{ __('Active') }}</label>
		<div class="form-check form-switch">
			<input class="form-check-input coevs-switch" @checked(old('status',$social->status)) type="checkbox" name="status" value="1" id="edit_social_status">
		</div>
	</div>
	
	{{-- Submit --}}
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<x-icon name="check" height="20" width="20"/>
			{{ __('Update Now') }}
		</button>
	</div>
</form>
