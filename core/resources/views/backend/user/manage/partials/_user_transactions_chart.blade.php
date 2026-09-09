<div class="card shadow-sm">
	<div class="card-body ">
		<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
			<h5 class="card-title mb-0 fw-semibold text-capitalize">
				{{ __('Transaction Summary') }}
			</h5>
            <div class="btn-toolbar" role="toolbar">
                <div class="input-group">
                    <input type="hidden" id="wallet-hidden-daterange">
                    <div id="user-trx-reportrange"
                         class="report-range form-control d-flex align-items-center justify-content-between cursor-pointer">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-primary"></i>
                            <span class="text-nowrap flex-grow-1">{{ __('Loading') }}...</span>
                        </div>
                        <x-icon name="angle-down" class="text-muted flex-shrink-0 ms-2"/>
                    </div>
                </div>
            </div>
		</div>
		<div id="user-trx-chart"></div>
	</div>
</div>

@push('scripts')
    @include('backend.user.manage.partials._user_transactions_chart_script')
@endpush

