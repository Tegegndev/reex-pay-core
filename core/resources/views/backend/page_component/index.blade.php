@extends('backend.layouts.app')
@section('title', __('Page Component'))
@section('content')
	<div class="clearfix my-3">
		<div class="fs-4 fw-semibold float-start">{{ __('Page Component') }}</div>
		@can('component-manage')
			<a href="{{ route('admin.page.component.create') }}" class="btn btn-primary float-end">
				<x-icon name="add" class="icon"/>{{ __('Add New') }}</a>
		@endcan
	</div>
	<div class="card border-0 mb-4">
		<div class="card-body px-2">
			<div class="table-responsive">
				<table class="table user-table align-items-center">
					<thead class="table-light">
					<tr>
						<th>{{ __('Icon') }}</th>
						<th>{{ __('Name') }}</th>
						<th>{{ __('Type') }}</th>
						<th>{{ __('Status') }}</th>
						@can('component-manage')
							<th>{{ __('Action') }}</th>
						@endcan
					</tr>
					</thead>
					<tbody>
					@foreach($components as $component)
						<tr>
							<td>
								<div class="avatar-md">
									<img src="{{ asset($component->component_icon) }}" alt="{{ $component->component_name }}" class="img-fluid avatar-img">
								
								</div>
							</td>
							<td>{{ $component->component_name }}</td>
							<td>{{ $component->type }}</td>
							<td>
								<span class="badge {{ $component->is_active ? 'bg-success' : 'bg-danger' }}">
								    {{ $component->is_active ? __('Active') : __('Inactive') }}
								</span>
							</td>
							@can('component-manage')
								<td>
									@unless($component->is_protected)
										<a href="{{ route('admin.page.component.edit',$component->id) }}" class="btn  btn-primary">
											<x-icon name="manage" class="icon"/>{{ __('Manage') }}
										</a>
									@else
										<button disabled class="btn  btn-warning">
											<i class="fa-solid fa-lock me-1"></i> {{ __('Protected') }}
										</button>
									@endif
									
									@if($component->type == \App\Enums\ComponentType::Dynamic)
										<a href="javascript:void(this)" class="btn  btn-danger text-white delete" data-url="{{ route('admin.page.component.destroy', $component->id) }}">
											<x-icon name="delete-2" class="icon text-white"/>{{ __('Delete') }}
										</a>
									@endif
								</td>
							@endcan
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

@endsection
