<div class="card border mb-4">
	<div class="card-header d-flex justify-content-between align-items-center fixed-header bg-light py-2 px-3">
		<h2 class="h5 mb-0"> {{ __('Password Change') }}</h2>
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('admin.profile.password.update') }}">
			@csrf
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="new_password" class="form-label">{{ __('New Password') }}</label>
					<input id="new_password" class="form-control" name="new_password" type="password" placeholder="Enter New Password" required>
				</div>
				<div class="col-md-6 mb-3">
					<label for="confirm_password" class="form-label">{{ __('Confirm Password') }}</label>
					<input id="confirm_password" class="form-control" name="new_password_confirmation" type="password" placeholder="Enter Confirm Password" required>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 mb-3">
					<label for="old_password" class="form-label">{{ __('Old Password') }}</label>
					<input id="old_password" class="form-control" name="old_password" type="password" placeholder="Enter Old Password" required>
				</div>
			</div>
			<div class="mt-3 text-end">
				<button class="btn btn-primary" type="submit"><x-icon name="check" class="icon"/> {{ __('Update Password') }}</button>
			</div>
		</form>
	</div>
</div>
