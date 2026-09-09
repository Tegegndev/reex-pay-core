@extends('backend.layouts.app')
@section('title', __('All KYC'))
@section('content')
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="d-flex justify-content-between align-items-center my-3">
					<h1 class="h3 fw-bold">{{ __('All KYC') }}</h1>
				</div>
				<div class="card">
					<div class="card-body">
						<div class="d-flex justify-content-end">
							<form action="{{ route('admin.kyc.index') }}" method="GET" class="row mb-3 g-3 align-items-end">
								<div class="col-auto">
									<input type="hidden" name="daterange" value="{{ request('daterange') }}">
									<div class="input-group">
										<div id="reportrange" class="form-control">
											<i class="fa-solid fa-calendar-days"></i>
											<span></span>
											<i class="fa-solid fa-angle-down ms-2"></i>
										</div>
									</div>
								</div>
								
								<div class="col-auto">
									<input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search') }} ...">
								</div>
								
								<div class="col-auto">
									<button type="submit" class="btn btn-primary">
										<i class="fa-solid fa-magnifying-glass"></i> {{ __('Search') }}
									</button>
								</div>
							</form>
						</div>
						
						<div class="table-responsive">
							<table class="table caption-top mb-0">
								<thead class="table-light fw-semibold text-nowrap">
								<tr class="align-middle">
									<th>{{ __('User') }}</th>
									<th>{{ __('KYC Type | User Type') }}</th>
									<th>{{ __('Submitted At') }}</th>
									@can('kyc-action')
										<th>{{ __('Action') }}</th>
									@endcan
								</tr>
								</thead>
								<tbody>
								@forelse($kycRequests as $submission)
									<tr>
										<td>
											<div class="d-flex align-items-center">
												<img src="{{ asset($submission->user->avatar_alt) }}"
												     alt="{{ $submission->user->name }}" class="rounded-circle me-2"
												     width="40" height="40">
												<div>
													<div>{{ $submission->user->name }}</div>
													<small class="text-muted">{{ $submission->user->email }}</small>
												</div>
											</div>
										</td>
										<td>
											<div>{{ $submission->kycTemplate->title }}</div>
											<span class="badge rounded-pill bg-{{ $submission->user->role->color() }}">{{ $submission->user->role->title() }}</span>
										</td>
										<td>
											<div>{{ $submission->created_at->format('Y-m-d H:i') }}</div>
											<div class="small text-muted">{{ $submission->created_at->diffForHumans() }}</div>
										</td>
										@can('kyc-action')
											<td>
												<button type="button" class="btn btn-primary" data-coreui-toggle="modal"
												        data-coreui-target="#review-{{ $submission->id }}">
													<i class="fa-solid fa-eye"></i>
													{{ __('View') }}
												</button>
												
												@include('backend.kyc.partials._review_modal',['action' => 'view'])
											</td>
										@endcan
									</tr>
								@empty
									<tr>
										<td colspan="4" class="text-center text-muted py-5">
											<h5>{{ __('No pending KYC requests found.') }}</h5>
										</td>
									</tr>
								@endforelse
								</tbody>
							</table>
						</div>
						
						<div class="d-flex justify-content-end mt-3">
							{{ $kycRequests->links() }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

