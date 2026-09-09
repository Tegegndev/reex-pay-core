<div class="card border mb-4">
	<div class="card-header d-flex justify-content-between align-items-center fixed-header bg-light py-2 px-3">
		<h2 class="h5 m-0">{{ __('Profile information Edit') }}</h2>
	</div>
	
	<div class="card-body">
		<form method="POST" action="{{ route('admin.profile.info.update') }}" enctype="multipart/form-data">
			@csrf
			<div class="row">
				
				<div class="col-md-6 mb-3">
					<label for="phone" class="form-label">{{ __('Avatar') }}</label>
					<x-img name="avatar" :old="auth()->user()->avatar"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="name" class="form-label">{{ __('Name') }}</label>
					<input  class="form-control" name="name" value="{{ auth()->user()->name }}" id="name" type="text" placeholder="Enter your first name" required>
				</div>
				<div class="col-md-6 mb-3">
					<label for="email" class="form-label">{{ __('Email') }}</label>
					<input class="form-control" name="email" value="{{ auth()->user()->email }}" id="email" type="email" placeholder="name@company.com" required>
				</div>
			</div>
			
			<div class="mt-3 text-end">
				<button class="btn btn-primary" type="submit"><x-icon name="check" class="icon"/> {{ __('Update Profile') }}</button>
			</div>
		</form>
	</div>
</div>