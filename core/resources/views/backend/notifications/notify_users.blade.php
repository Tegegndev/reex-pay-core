@extends('backend.layouts.app')
@section('title', __('Notify Users'))

@section('content')
	<div class="my-3">
		<h3 class="fw-semibold mb-3">{{ __('Notify Users') }}</h3>
	</div>
	
	<!-- Notify Users Card -->
	<div class="card shadow-sm border-0 mb-4">
		<div class="card-body py-4 px-3 px-md-5">
			<form action="{{ route('admin.notifications.notifyToUser.send') }}" method="POST">
				@csrf
				<div class="row g-3">
					
					<!-- User Type -->
					<div class="col-md-6">
						<label for="user_type" class="form-label">{{ __('User Type') }}</label>
						<select id="user_type" name="user_type" class="form-select">
							<option value="all">{{ __('All') }}</option>
							@foreach(\App\Enums\UserRole::options() as $value => $label)
								<option value="{{ $value }}">{{ $label }}</option>
							@endforeach
						</select>
					</div>
					
					<!-- Notification Type -->
					<div class="col-md-6">
						<label for="notify_type" class="form-label">{{ __('Notification Type') }}</label>
						<select id="notify_type" name="notify_type" class="form-select">
							<option value="email">{{ __('Email') }}</option>
							<option value="push">{{ __('Push Notification') }}</option>
						</select>
					</div>
					
					<!-- Title -->
					<div class="col-12" id="title_row">
						<label for="title" class="form-label">{{ __('Title') }}</label>
						<input type="text" id="title" name="title" class="form-control" placeholder="Notification title...">
					</div>
					
					
					<!-- Message -->
					<div class="col-12">
						<label for="message" class="form-label">{{ __('Message') }}</label>
						<textarea id="message" name="message" class="form-control" rows="4" placeholder="Write your message here..." required></textarea>
					</div>
					
					<!-- Schedule At -->
					<div class="col-md-6">
						<label for="schedule_at" class="form-label">{{ __('Schedule At') }}</label>
						<input type="datetime-local" id="schedule_at" name="schedule_at" class="form-control">
					</div>
					
					<!-- Submit Button -->
					<div class="col-12 d-flex justify-content-end mt-2">
						<button type="submit" class="btn btn-primary px-4">
							<i class="fa-solid fa-paper-plane me-2"></i> {{ __('Send Notification') }}
						</button>
					</div>
				
				</div>
			</form>
		</div>
	</div>
@endsection
@push('scripts')
	<script>
        "use strict";
        document.addEventListener('DOMContentLoaded', function () {
            const notifyType = document.getElementById('notify_type');
            const titleRow = document.getElementById('title_row');

            function toggleTitleField() {
                if (notifyType.value === 'push') {
                    titleRow.classList.add('d-none');
                } else {
                    titleRow.classList.remove('d-none');
                }
            }

            // On page load
            toggleTitleField();

            // On change
            notifyType.addEventListener('change', toggleTitleField);
        });
	</script>
@endpush
