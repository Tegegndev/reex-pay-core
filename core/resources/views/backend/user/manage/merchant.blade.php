@extends('backend.user.manage')
@section('user_manage_content')
	<div class="card-body">
		{{-- Transactions Table --}}
		<div class="table-responsive">
			<table class="table caption-top mb-0">
				<thead class="table-light fw-semibold text-nowrap">
				<tr class="align-middle">
					<th>{{ __('Merchant Info') }}</th>
					<th>{{ __('Status | Merchant ID') }}</th>
					<th>{{ __('Time') }}</th>
					<th>{{ __('Action') }}</th>
				</tr>
				</thead>
				<tbody>
				@forelse($user->merchants as $merchant)
					@php
						$statusColor = $merchant->status->color();
					@endphp
					<tr class="align-middle">
						<td>
							<div class="d-flex align-items-center">
								<img class="rounded-circle shadow-sm me-2" width="36" height="36"
								     src="{{ asset($merchant->business_logo) }}" alt="User Avatar">
								<div>
									<div class="text-nowrap">{{ $merchant->business_name }}</div>
									<div class="small text-muted ">{{ $merchant->site_url }}</div>
								</div>
							</div>
						</td>
						<td>
							<div class="text-primary-emphasis fw-bold">
								{{ $merchant->merchant_key }}
							</div>
							<div class="small text-muted">
								<span class="badge bg-{{  $statusColor }}">
                                         {{ $merchant->status }}
                                </span>
							</div>
						</td>
						<td>
							<div>{{ $merchant->created_at->format('Y-m-d H:i') }}</div>
							<div class="small text-muted">{{ $merchant->created_at->diffForHumans() }}</div>
						</td>
							<td>
								<button type="button" class="btn btn-primary" data-coreui-toggle="modal"
								        data-coreui-target="#review-{{ $merchant->id }}">
									<x-icon name="manage" height="20"/>
								</button>
								
								@include('backend.merchant.partials._review_modal')
							
							</td>
					</tr>
				@empty
					<tr>
						<td colspan="5" class="text-center text-muted py-5">
							{{ __('No Merchant found.') }}
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
	
	</div>
@endsection
