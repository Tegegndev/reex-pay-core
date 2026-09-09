@extends('backend.layouts.app')
@section('title')
	{{ __('Social Links') }}
@endsection
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{ __('Social Links') }}</h1>
			</div>
		@can('social-manage')
			<div class="btn-toolbar mb-md-0 mb-2">
				<button type="button" class="btn btn-primary d-inline-flex align-items-center"
				        data-coreui-toggle="modal" data-coreui-target="#create-social-modal">
					<x-icon name="add" class="me-1" height="24"/>
					{{ __('Add New') }}
				</button>
			</div>
		@endcan
		
		</div>
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table user-table align-items-center">
					<thead>
					<tr>
						<th>{{ __('Name') }}</th>
						<th>{{ __('Icon Class') }}</th>
						<th>{{ __('URL') }}</th>
						<th>{{ __('Target') }}</th>
						<th>{{ __('Status') }}</th>
						@can('social-manage')
							<th>{{ __('Action') }}</th>
						@endcan
					</tr>
					</thead>
					<tbody>
					@forelse($socials as $social)
						<tr>
							<td class="fw-bold">{{ $social->name }}</td>
							<td class="fw-bold">{{ $social->icon_class }}</td>
							<td class="fw-bold">{{ $social->url }}</td>
							<td>
                                <span class="badge bg-{{ $social->target->colorClass() }}">
                                    {{ $social->target->label() }}
                                </span>
							</td>
							<td>
                                <span class="badge bg-{{ $social->status ? 'success' : 'danger' }}">
                                    {{ strtoupper($social->status ? 'ACTIVE' : 'INACTIVE') }}
                                </span>
							</td>
							@can('social-manage')
								<td>
									<div class="d-flex gap-2">
										<a href="javascript:void(0)" class="btn btn-primary edit-modal"
										   data-edit-url="{{ route('admin.social.edit', $social->id) }}">
											<x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
										</a>
										<a href="javascript:void(0)" class="btn btn-danger text-white delete"
										   data-url="{{ route('admin.social.destroy', $social->id) }}">
											<x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
										</a>
									</div>
								</td>
							@endcan
						</tr>
					@empty
						<tr>
							<td colspan="6">
								<h4 class="text-center text-muted py-3">{{ __('No Data Available') }}</h4>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	@can('social-manage')
		{{-- Create Modal --}}
		@include('backend.social.partials._create_modal')
		
		{{-- Edit Modal --}}
		@include('backend.social.partials._edit_modal')
	@endcan
@endsection

@push('scripts')
	@can('social-manage')
		<script>
	        editFormByModal('edit-social-modal', 'edit-append');
		</script>
	@endcan
@endpush
