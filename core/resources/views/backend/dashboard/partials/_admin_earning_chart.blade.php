<div class="col-sm-12 col-md-6">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-body px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <h5 class="card-title mb-0 fw-semibold text-capitalize">
                    {{ __('Daily Fee Earnings') }}
                </h5>
                <div class="btn-toolbar" role="toolbar">
                    <div class="input-group">
                        <input type="hidden" name="daterange" id="hidden-daterange" value="{{ request('daterange') }}">
                        <div id="report-earning-range" class="report-range form-control d-flex align-items-center justify-content-between cursor-pointer" >
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-calendar-days text-primary"></i>
                                <span class="text-nowrap flex-grow-1">{{ __('Loading') }}...</span>
                            </div>
                            <x-icon name="angle-down" class="text-muted flex-shrink-0 ms-2"/>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="fee-earnings-chart"></div>
        </div>
    </div>
</div>


@push('scripts')
    @include('backend.dashboard.partials._admin_earning_chart_script')
@endpush



