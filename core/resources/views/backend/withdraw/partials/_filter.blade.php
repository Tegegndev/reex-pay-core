<div class="d-flex justify-content-end mb-3">
	<form action="{{ route('admin.deposit.history') }}" method="GET" class="row g-2 g-md-3">
		{{-- Date Range Picker --}}
		<div class="col-md-6 col-xl-auto">
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
		
		
		{{-- Status Filter --}}
		<div class="col-md-6 col-xl-auto">
			<x-form.select name="status" label="{{ __('Withdraw Status') }}"
			               :options="\App\Enums\TrxStatus::options()"
			               :selected="request('status')"/>
		</div>
		
		{{-- Search Input --}}
		<div class="col-md-6 col-xl-auto">
			<div class="input-group">
				<input type="text" name="search" value="{{ request('search') }}" class="form-control"
				       placeholder="{{ __('Search') }}...">
				<button type="submit" class="btn btn-primary">
					<i class="fa-solid fa-magnifying-glass"></i>
				</button>
			</div>
		</div>
	</form>
</div>