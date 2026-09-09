<div class="mt-4 border rounded bg-light">
	<h5 class="fw-bold  text-start border-bottom px-3 pt-3 pb-2">
		<i class="fa-solid fa-lock me-2"></i> {{ __('User Controls') }}
	</h5>
	
	<div class="scrollable-feature-list px-3 pb-3">
		@foreach($user->features as $feature)
			@php
				$title = ucwords(str_replace('_', ' ', $feature->feature))
			@endphp
			<div class="d-flex justify-content-between align-items-center py-2 @if(!$loop->last) border-bottom @endif">
				<div class="text-start">
					<label for="feature_{{ $feature->id }}" class="fw-bold d-block mb-1">
						{{ __($title) }}
					</label>
					<p class="text-muted small mb-0">
						{{ __($feature->description) }}
					</p>
				</div>
				<div class="form-check form-switch ms-3">
					<input class="form-check-input feature-switch"
					       type="checkbox"
					       id="feature_{{ $feature->id }}"
					       data-feature="{{ $feature->feature }}"
					       data-user-id="{{ $user->id }}"
					       aria-label="{{ __('Toggle feature status') }}"
						@checked($feature->dynamic_status)>
				</div>
			</div>
		@endforeach
	</div>
</div>