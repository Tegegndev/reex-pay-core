@extends('backend.virtual_card.index')
@section('title',  __('Awaiting Virtual Card Requests'))
@section('virtual_card_header')
	<div class="clearfix my-3">
		<div class="fs-3 fw-semibold float-start">
			{{ __('Awaiting Virtual Card Requests') }}
		</div>
	</div>
@endSection
@section('virtual_card_content')
	<div class="card-body">
		<div class="d-flex justify-content-end mb-3">
			<form action="{{ route('admin.virtual-card.requests.awaiting') }}" method="GET" class="row g-2 g-md-3">
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
				{{-- Search Input --}}
				<div class="col-md-6 col-xl-auto">
					<div class="input-group">
						<input type="text" name="search" value="{{ request('search') }}" class="form-control"
						       placeholder="{{ __('Search by user, wallet, or status...') }}">
						<button type="submit" class="btn btn-primary">
							<i class="fa-solid fa-magnifying-glass"></i>
						</button>
					</div>
				</div>
			</form>
		</div>
		{{-- Requests Table --}}
		<div class="table-responsive">
			<table class="table caption-top mb-0">
				<thead class="table-light fw-semibold text-nowrap">
				<tr class="align-middle">
					<th>{{ __('User | Request ID') }}</th>
					<th>{{ __('Wallet | Card') }}</th>
					<th>{{ __('Status | Note') }}</th>
					<th>{{ __('Requested At') }}</th>
					<th>{{ __('Action') }}</th>
				</tr>
				</thead>
				<tbody>
				@forelse($requests as $request)
					<tr class="align-middle">
						<td>
							<div class="d-flex align-items-center">
								<img class="rounded-circle shadow-sm me-2" width="36" height="36"
								     src="{{ asset($request->user->avatar_alt) }}" alt="User Avatar">
								<div>
									<div class="text-nowrap fw-medium">{{ $request->user->name }}</div>
									<div class="small text-muted text-uppercase">#{{ $request->uuid }}</div>
								</div>
							</div>
						</td>
						<td>
							<div>
								<span class="badge bg-light text-dark border">
									{{ $request->wallet->currency->code ?? 'N/A' }}
								</span>
								@if($request->card)
									<span class="badge bg-success ms-2">•••• {{ $request->card->last4 }}</span>
								@endif
							</div>
							@if($request->card)
								<div class="small text-muted">{{ $request->card->expiry_month }}/{{ $request->card->expiry_year }}</div>
							@endif
						</td>
						<td>
							<div>
								<span class="badge bg-{{ $request->status->badgeColor() }}">
									{{ $request->status->label() }}
								</span>
							</div>
							@if($request->admin_note)
								<div class="small text-danger">{{ $request->admin_note }}</div>
							@endif
						</td>
						<td>
							<div>{{ $request->created_at->format('Y-m-d H:i') }}</div>
							<div class="small text-muted">{{ $request->created_at->diffForHumans() }}</div>
						</td>
						<td>
							<button type="button" class="btn btn-primary" data-coreui-toggle="modal"
							        data-coreui-target="#review-request-{{ $request->uuid }}">
								<i class="fa-duotone fa-arrow-right-from-bracket"></i>
								{{ __('Review') }}
							</button>
							@include('backend.virtual_card.partials._review_modal', ['request' => $request])
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="5" class="text-center text-muted py-5">
							<h4 class="text-muted">{{ __('No virtual card requests found.') }}</h4>
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
		<div class="d-flex justify-content-end mt-3">
			{{ $requests->links() }}
		</div>
	</div>
@endsection
