@extends('backend.layouts.app')
@section('title')
	{{  __('Navigation Manage') }}
@endsection
@section('content')
	
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{  __('Navigation Manager') }}</h1>
			</div>
			<div class="btn-toolbar  mb-md-0 mb-2 ">
				<button type="button" class="btn btn-primary d-inline-flex align-items-center" data-coreui-toggle="modal" data-coreui-target="#new-nav-modal">
					<x-icon name="plus"/>
					{{ __('Add New') }}
				</button>
			</div>
		
		</div>
	</div>
	<div class="card border-0  mb-4">
		<div class="card-body">
			<div class="table-responsive rounded">
				<table class="table mb-0 caption-top">
					<thead>
					<tr>
						<th class="text-center">
							<x-icon name="up-down" height="20" width="20"/>
						</th>
						<th>{{ __('Name') }}</th>
						<th>{{ __('Type') }}</th>
						<th>{{ __('Target') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody id="navigation-sortable">
					@forelse ($navigations as $key => $nav)
						<tr data-id="{{ $nav->id }}">
							<td class="text-muted text-center">
								<i class="fa-solid fa-grip-vertical drag-handle fs-5" title="Drag up/down to sort" data-coreui-toggle="tooltip"></i>
							</td>
							<td>{{ $nav->label }}</td>
							<td>
								@if ($nav->is_custom_url)
									<span class="badge bg-info">{{ __('External') }}</span>
									<small class="d-block text-muted">{{ Str::limit($nav->slug, 40) }}</small>
								@elseif ($nav->page)
									<span class="badge bg-success">{{ __('Page') }}</span>
									<small class="d-block text-muted">{{ $nav->page->label }}</small>
								@else
									<span class="badge bg-secondary">{{ __('N/A') }}</span>
								@endif
							</td>
							<td>
                                <span class="badge bg-{{ $nav->target->colorClass() }}">
                                    {{ $nav->target->label() }}
                                </span>
							</td>
							<td>
								@if ($nav->is_active)
									<span class="badge bg-success">{{ __('Active') }}</span>
								@else
									<span class="badge bg-danger">{{ __('Inactive') }}</span>
								@endif
							</td>
							<td>
								<div class="btn-group btn-group">
									<a href="#" class="btn btn-primary edit-modal" data-edit-url="{{ route('admin.navigation.site.edit', $nav->id) }}">
										<x-icon name="edit" height="20" width="20"/>
										{{ __('Edit') }}
									</a>
									<a href="#" class="btn btn-danger delete text-white" data-url="{{ route('admin.navigation.site.destroy',$nav->id) }}">
										<x-icon name="x" height="20" width="20"/>
										{{ __('Delete') }}
									</a>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="text-center text-muted py-4">{{ __('No navigation items found.') }}</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	{{-- New Nav Modal --}}
	@include('backend.navigation.partials._new_nav_modal')
	
	{{-- New Nav Modal --}}
	@include('backend.navigation.partials._edit_nav_modal')

@endsection
@push('scripts')
	@include('backend.navigation.partials._script')
@endpush


