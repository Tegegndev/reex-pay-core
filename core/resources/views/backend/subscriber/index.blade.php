@extends('backend.layouts.app')
@section('title', __('Subscribers History'))
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<h1 class="h4 mb-3">{{ __('Subscribers History') }}</h1>
		</div>
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body">
			
			{{-- Filters & Send All Button --}}
			<div class="d-flex justify-content-between flex-wrap mb-3 align-items-end">

				@can('subscriber-manage')
					{{-- Left side: Filters --}}
					<div class="mb-2">
						<label class="form-label small d-block invisible">.</label>
						<button type="button" class="btn btn-success text-white" data-coreui-toggle="modal" data-coreui-target="#sendMailModal">
							<i class="fa-solid fa-paper-plane me-1"></i> {{ __('Send Mail to All Subscribers') }}
						</button>
					</div>
				@endcan
				
				
				{{-- Right side: Send Mail to All Button --}}
				<form action="{{ route('admin.subscriber.index') }}" method="GET" class="d-flex flex-wrap gap-2">
					{{-- Date Range --}}
					<div>
						<label for="reportrange" class="form-label small">{{ __('Date Range') }}</label>
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
					
					{{-- Search --}}
					<div>
						<label for="search" class="form-label small">{{ __('Search') }}</label>
						<div class="input-group">
							<input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Search...') }}">
							<button type="submit" class="btn btn-primary">
								<i class="fa-solid fa-magnifying-glass"></i>
							</button>
						</div>
					</div>
				</form>
			</div>
			
			{{-- Transactions Table --}}
			<div class="table-responsive">
				<table class="table  border mb-0">
					<thead class="table-light fw-semibold">
					<tr class="align-middle text-nowrap">
						<th>{{ __('Email') }}</th>
						<th>{{ __('IP') }}</th>
						<th>{{ __('Subscribed') }}</th>
						@can('subscriber-manage')
							<th>{{ __('Actions') }}</th>
						@endcan
					</tr>
					</thead>
					<tbody>
					@forelse($subscribers as $subscriber)
						<tr class="align-middle">
							<td>
								<div class="text-truncate">
									{{ $subscriber->email }}
								</div>
							</td>
							
							<td>
								{{ $subscriber->ip_address }}
								<a href="https://whatismyipaddress.com/ip/{{ $subscriber->ip_address }}"
								   target="_blank"
								   class="btn btn-link p-0"
								   data-coreui-toggle="tooltip"
								   data-coreui-placement="top"
								   title="Lookup IP">
									<i class="fa-solid fa-search"></i>
								</a>
							</td>
							<td>
								<div>{{ $subscriber->subscribed_at->format('Y-m-d H:i') }}</div>
								<div class="small text-muted">{{ $subscriber->subscribed_at->diffForHumans() }}</div>
							</td>
							@can('subscriber-manage')
								<td>
									<div class="d-flex gap-2">
										<a href="javascript:void(0)" class="btn btn-primary single-mail"
										   data-id="{{ $subscriber->id }}"
										   data-email="{{ $subscriber->email }}">
											<x-icon name="mail" height="20" width="20" class="me-1"/> {{ __('Send Mail') }}
										</a>
										
										<a href="javascript:void(0)"
										   class="btn btn-danger text-white delete"
										   data-url="{{ route('admin.subscriber.delete', $subscriber->id) }}">
											<x-icon name="delete-3" height="20" width="20" class="me-1"/> {{ __('Delete') }}
										</a>
									</div>
								</td>
							@endcan
						
						</tr>
					@empty
						<tr>
							<td colspan="5" class="text-center py-3">
								<div class="text-center py-5">
									<x-icon name="no-data-found" height="200"/>
									<h5 class="text-muted mt-2">{{ __('No Data found') }}</h5>
								</div>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
			
			{{-- Pagination --}}
			<div class="d-flex justify-content-end mt-3">
				{{ $subscribers->links() }}
			</div>
		</div>
	</div>
	
	@can('subscriber-manage')
		{{-- Send Mail Modal --}}
		@include('backend.subscriber.partials._send_mail_modal')
	@endcan
	
@endsection
@push('scripts')
	@can('subscriber-manage')
		@include('backend.subscriber.partials._scripts')
	@endcan
@endpush

