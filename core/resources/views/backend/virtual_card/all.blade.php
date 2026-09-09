@extends('backend.virtual_card.index')
@section('title',  __('All Virtual Card Requests'))
@section('virtual_card_header')
	<div class="clearfix my-3">
		<div class="fs-3 fw-semibold float-start">
			{{ __('All Virtual Card Requests') }}
		</div>
		<div class="float-end">
			<a href="{{ route('admin.virtual-card.requests.awaiting') }}" class="btn btn-outline-primary">
				<i class="fa-solid fa-clock-rotate-left me-1"></i> {{ __('View Pending') }}
			</a>
		</div>
	</div>
@endsection
@section('virtual_card_content')
	<div class="card-body">
		<div class="d-flex justify-content-end mb-3">
			<form action="{{ route('admin.virtual-card.requests.all') }}" method="GET" class="row g-2 g-md-3">
				<div class="col-md-6 col-xl-auto">
					<x-form.select name="status"
					               :options="$statuses"
					               :selected="request('status')"
					               :includeBlank="true"
					/>
				</div>
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
				<div class="col-md-6 col-xl-auto">
					<div class="input-group">
						<input type="text" name="search" value="{{ request('search') }}"
						       class="form-control" placeholder="{{ __('Search by user, email, card...') }}">
						<button type="submit" class="btn btn-primary">
							<i class="fa-solid fa-magnifying-glass"></i>
						</button>
					</div>
				</div>
			</form>
		</div>
		
		<div class="table-responsive">
			<table class="table align-middle">
				<thead class="table-light">
				<tr>
					<th>{{ __('User') }}</th>
					<th>{{ __('Card Details') }}</th>
					<th>{{ __('Status') }}</th>
					<th>{{ __('Requested') }}</th>
					<th class="text-end">{{ __('Actions') }}</th>
				</tr>
				</thead>
				<tbody>
				@forelse($requests as $request)
					<tr>
						<td>
							<div class="d-flex align-items-center">
								<img src="{{ $request->user->avatar_alt }}"
								     class="rounded-circle me-2" width="32" height="32"
								     alt="{{ $request->user->name }}">
								<div>
									<div class="fw-medium">{{ $request->user->name }}</div>
									<small class="text-muted">#{{ $request->uuid }}</small>
								</div>
							</div>
						</td>
						<td>
							<div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark">
                                    {{ $request->wallet->currency->code }}
                                </span>
								@if($request->card)
									<span class="badge bg-success">•••• {{ $request->card->last4 }}</span>
								@endif
							</div>
							@if($request->card)
								<small class="text-muted">
									{{ $request->card->brand ?? '' }} •
									{{ $request->card->expiry_month }}/{{ substr($request->card->expiry_year, -2) }}
								</small>
							@endif
						</td>
						<td>
                            <span class="badge bg-{{ $request->status->badgeColor() }}">
                                {{ $request->status->label() }}
                            </span>
							@if($request->admin_note)
								<div class="small text-muted mt-1" title="{{ $request->admin_note }}">
									<i class="fa-solid fa-note-sticky"></i>
									{{ Str::limit($request->admin_note, 30) }}
								</div>
							@endif
						</td>
						<td>
							<div>{{ $request->created_at->format('M d, Y') }}</div>
							<small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
						</td>
						<td class="text-end">
							<button type="button" class="btn btn-primary" data-coreui-toggle="modal"
							        data-coreui-target="#review-request-{{ $request->uuid }}">
								<i class="fa-duotone fa-arrow-right-from-bracket"></i>
								{{ __('Details') }}
							</button>
							@include('backend.virtual_card.partials._review_modal', ['request' => $request])
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="5" class="text-center py-5">
							<div class="text-muted">
								<i class="fa-regular fa-inbox fa-3x mb-3"></i>
								<h5>{{ __('No requests found') }}</h5>
							</div>
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
		
		@if($requests->hasPages())
			<div class="d-flex justify-content-center mt-4">
				{{ $requests->withQueryString()->links() }}
			</div>
		@endif
	</div>
	
@endsection