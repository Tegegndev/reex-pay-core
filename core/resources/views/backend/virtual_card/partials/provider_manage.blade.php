<form action="{{ route('admin.virtual-card.provider.update', $provider) }}" method="post">
	@csrf
	@method('PUT')
	
	<div class="mb-3">
		<label class="form-label" for="name">{{ __('Provider Name') }}</label>
		<input class="form-control" type="text" name="name" value="{{ old('name', $provider->name) }}" required>
	</div>
	<div class="row mb-3">
		<div class="col-md-12">
			<label class="form-label" for="currency_symbol">{{ __('Card Issue Fee:') }}</label>
			<div class="input-group">
				<input type="text" class="form-control" name="issue_fee" value="{{ $provider->issue_fee }}"
				       oninput="this.value = validateDouble(this.value)"
				       aria-label="Amount (to the nearest dollar)">
				<span class="input-group-text">{{ siteCurrency() }}</span>
			</div>
		</div>
	</div>
	
	<div class="row mb-3">
		<div class="col-lg-4 col-md-4 col-4">
			<label class="form-label" for="status">{{ __('Status') }}</label>
			<div class="form-check form-switch">
				<input class="form-check-input coevs-switch" type="checkbox" name="status"
				       @checked($provider->status) value="1">
			</div>
		</div>
	</div>
	<div class="text-end">
		<button class="btn btn-primary" type="submit">
			<x-icon name="check" height="20"/> {{ __('Update Provider') }}
		</button>
	</div>
</form>
