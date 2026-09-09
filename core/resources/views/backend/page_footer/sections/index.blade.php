@extends('backend.layouts.app')
@section('title')
	{{ __('Footer Manage') }}
@endsection
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{ __('Footer Manage') }}</h1>
			</div>
			<div class="btn-toolbar  mb-md-0 mb-2">
				<button type="button" class="btn btn-primary d-inline-flex align-items-center"
				        data-coreui-toggle="modal" data-coreui-target="#create-footer-section-modal">
					<x-icon name="add" class="me-1" height="24"/>
					{{ __('Add New') }}
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
						<th>{{ __('Title') }}</th>
						<th>{{ __('Type') }}</th>
						<th>{{ __('Total Item') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody id="footer-section-sortable">
					@forelse($sections as $section)
						<tr data-id="{{ $section->id }}">
							<td class="text-muted text-center">
								<i class="fa-solid fa-grip-vertical drag-handle fs-5" title="Drag up/down to sort" data-coreui-toggle="tooltip"></i>
							</td>
							<td>{{ $section->title_text }}</td>
							<td><span class="badge bg-info">{{ $section->type->label() }}</span></td>
							<td>
								<span class="badge bg-secondary">
									{{ $section->items_count }}
								</span>
							</td>
							<td>
                                    <span class="badge bg-{{ $section->status ? 'success' : 'danger' }}">
                                        {{ strtoupper($section->status ? 'ACTIVE' : 'INACTIVE') }}
                                    </span>
							</td>
							<td>
								<div class="btn-group" role="group" aria-label="Actions">
									<a href="javascript:void(0)" class="btn  btn-primary edit-modal"
									   data-edit-url="{{ route('admin.page.footer.section.edit', $section->id) }}">
										<x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
									</a>
									<a href="{{ route('admin.page.footer.item.index',['footer_section' => $section->id]) }}" class="btn btn-secondary">
										<x-icon name="manage" height="20" width="20"/> {{ __('Item Manage') }}
									</a>
									<a href="javascript:void(0)" class="btn  btn-danger text-white delete"
									   data-url="{{ route('admin.page.footer.section.destroy', $section->id) }}">
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
	@include('backend.page_footer.sections.partials._create_modal')
	
	{{-- Edit Modal --}}
	@include('backend.page_footer.sections.partials._edit_modal')
@endsection

@section('scripts')
	@include('backend.page_footer.sections.partials._scripts')
@endsection
