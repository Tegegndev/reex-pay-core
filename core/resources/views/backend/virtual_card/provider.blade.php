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
		<div class="table-responsive">
			<table class="table table-striped rounded">
				<thead class="table-light">
				<tr class="text-muted ">
					<th>{{ __('Logo') }}</th>
					<th>{{ __('Name') }}</th>
					<th>{{ __('Brand') }}</th>
					<th>{{ __('Networks') }}</th>
					<th>{{ __('Currencies') }}</th>
					<th>{{ __('Fee') }}</th>
					<th>{{ __('Status') }}</th>
					<th>{{ __('Action') }}</th>
				</tr>
				</thead>
				<tbody>
				@foreach($providers as $provider)
					<tr>
						<td><img src="{{ $provider->logo_url }}" height="25"></td>
						<td>{{ $provider->name }}</td>
						<td>{{ $provider->brand ?? 'Multi' }}</td>
						<td>{{ $provider->networks_list }}</td>
						<td>{{ $provider->currencies_list }}</td>
						<td>{{ $provider->fee_formatted }}</td>
						<td>
		                <span class="badge bg-{{ $provider->status ? 'success' : 'danger' }}">
		                    {{ $provider->status ? __('Active') : __('Inactive') }}
		                </span>
						</td>
						<td>
							<button type="button" class="btn btn-primary edit-modal"
							        data-edit-url="{{ route('admin.virtual-card.provider.manage', $provider->id) }}">
								<x-icon name="manage" height="18" width="18"/> {{ __('Manage') }}
							</button>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		<div class="d-flex justify-content-end mt-3">
			{{ $providers->links() }}
		</div>
	</div>
	
	@include('backend.virtual_card.partials._manage_modal')
@endsection
@push('scripts')
	<script>
        editFormByModal('manage-virtual-card-modal', 'manage-virtual-card-data', true, true);
	</script>
@endpush
