
@php
	$routeName = Route::currentRouteName();
	$route = route($routeName);
	$statusFilter = !in_array($routeName, ['admin.merchant.active', 'admin.merchant.approved','admin.merchant.rejected']);
@endphp

<div class="d-flex justify-content-end mb-3">
	<form action="{{ $route }}" method="GET" class="row g-2 g-md-3">
		
		<div class="col-auto">
			<div class="input-group">
				<input type="hidden" name="daterange" value="{{ request('daterange') }}">
				<div id="reportrange" class="form-control d-flex align-items-center justify-content-between">
					<div class="d-flex align-items-center gap-2">
						<i class="fa-solid fa-calendar-days"></i>
						<span class="text-nowrap flex-grow-1"></span>
					</div>
					<x-icon name="angle-down" class="text-muted flex-shrink-0"/>
				</div>
			</div>
		</div>
		{{-- Status Select --}}
		@if($statusFilter)
			<div class="col-auto">
				<x-form.select
					name="status"
					:label="__('Merchant Status')"
					class="form-select pe-5"
					:options="App\Enums\MerchantStatus::options()"
					:selected="request('status', 'all')"
				/>
			</div>
		@endif
		{{-- Search Input --}}
		<div class="col-12 col-md-6 col-xl-auto">
			<div class="input-group">
				<input type="text" name="search" value="{{ request('search') }}"
				       class="form-control "
				       placeholder="{{ __('Search') }}..." aria-label="{{ __('Search...') }}">
				<button type="submit" class="btn btn-primary">
					<i class="fa-solid fa-magnifying-glass"></i>
				</button>
			</div>
		</div>
	</form>
</div>