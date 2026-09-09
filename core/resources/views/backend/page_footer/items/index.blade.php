@extends('backend.layouts.app')
@section('title')
	{{ __('Footer Items Manage') }}
@endsection
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{ __('Manage Items for:') }} <span class="text-primary">{{ $footerSection->title_text }}</span></h1>
			</div>
			<div class="btn-toolbar mb-md-0 mb-2">
				<a href="{{ route('admin.page.footer.section.index') }}" class="btn btn-secondary d-inline-flex align-items-center me-2">
					<x-icon name="back" class="me-1" height="24"/>
					{{ __('Back to Footer Sections') }}
				</a>
				<button type="button" class="btn btn-primary d-inline-flex align-items-center"
				        data-coreui-toggle="modal" data-coreui-target="#create-footer-item-modal">
					<x-icon name="add" class="me-1" height="24"/>
					{{ __('Add New Item') }}
				</button>
			
			</div>
		</div>
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table user-table align-items-center">
					<thead>
					<tr>
						<th class="text-center">
							<x-icon name="up-down" height="20" width="20"/>
						</th>
						<th>{{ __('Label') }}</th>
						<th>{{ __('Type') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody id="footer-item-sortable">
					@forelse($footerSection->items()->orderBy('order')->get()  as $item)
						<tr data-id="{{ $item->id }}">
							<td class="text-muted text-center">
								<i class="fa-solid fa-grip-vertical drag-handle fs-5" title="Drag to sort" data-coreui-toggle="tooltip"></i>
							</td>
							<td>
								{{ $item->label_text }}
							</td>
							<td>
								<span class="badge bg-{{ $item->url_type->colorClass() }}">{{ $item->url_type->label() }}</span>
								<small class="d-block text-muted">
									<a href="{{ $item->url }}" target="_blank" class="text-decoration-none">
										{{ $item->dynamic_label }}
									</a>
								</small>
							</td>
							<td>
                                <span class="badge bg-{{ $item->status ? 'success' : 'danger' }}">
                                    {{ strtoupper($item->status ? 'ACTIVE' : 'INACTIVE') }}
                                </span>
							</td>
							<td>
								<div class="btn-group" role="group" aria-label="Actions">
									<a href="javascript:void(0)" class="btn btn-primary edit-modal"
									   data-edit-url="{{ route('admin.page.footer.item.edit', $item->id) }}">
										<x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
									</a>
									<a href="javascript:void(0)" class="btn btn-danger text-white delete"
									   data-url="{{ route('admin.page.footer.item.destroy', $item->id) }}">
										<x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
									</a>
								</div>
							</td>
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
	
	{{-- Create Modal --}}
	@include('backend.page_footer.items.partials._create_modal')
	
	{{-- Edit Modal --}}
	@include('backend.page_footer.items.partials._edit_modal')
@endsection

@push('scripts')
	@include('backend.page_footer.items.partials._scripts')
@endpush
