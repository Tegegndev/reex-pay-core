@extends('backend.layouts.app')
@section('title', __('Blog Categories'))
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between mb-3">
			<h1 class="h4">{{ __('Blog Categories') }}</h1>
			@can('blog-category-manage')
				<button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#create-category-modal">
					<x-icon name="plus" class="me-1"/> {{ __('Add New') }}
				</button>
			@endcan
		</div>
		
		<div class="card border-0 shadow-sm">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table align-middle">
						<thead>
						<tr>
							<th>{{ __('Name') }}</th>
							<th>{{ __('Slug') }}</th>
							<th>{{ __('Total Blogs') }}</th>
							<th>{{ __('Status') }}</th>
							@can('blog-category-manage')
								<th>{{ __('Action') }}</th>
							@endcan
						</tr>
						</thead>
						<tbody id="category-list">
						@forelse($categories as $category)
							<tr data-id="{{ $category->id }}">
								<td>{{ $category->name_text }}</td>
								<td>{{ $category->slug }}</td>
								<td>
									<a href="{{ route('admin.blog.post.index', ['category' => $category->id]) }}" class="text-decoration-none">
										<span class="badge bg-info text-decoration-none fw-semibold">
										    {{ $category->blogs_count }}
										</span>
									</a>
								</td>
								<td>
                                    <span class="badge bg-{{ $category->status ? 'success' : 'danger' }}">
                                        {{ $category->status ? __('Active') : __('Inactive') }}
                                    </span>
								</td>
								@can('blog-category-manage')
									<td>
										<button type="button" class="btn btn-primary edit-modal"
										        data-edit-url="{{ route('admin.blog.category.edit', $category->id) }}">
											<x-icon name="edit" height="18"/> {{ __('Edit') }}
										</button>
										
										<button type="button" class="btn btn-danger delete text-white"
										        data-url="{{ route('admin.blog.category.destroy', $category->id) }}">
											<x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
										</button>
									</td>
								@endcan
							</tr>
						@empty
							<tr>
								<td colspan="4" class="text-center text-muted">{{ __('No categories found.') }}</td>
							</tr>
						@endforelse
						</tbody>
					</table>
					<div class="d-flex justify-content-end mt-3">
						{{ $categories->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	
	@can('blog-category-manage')
		{{-- Create Modal --}}
		@include('backend.blog.categories.partials._create_modal')
		
		{{-- Edit Modal --}}
		@include('backend.blog.categories.partials._edit_modal')
	@endcan

@endsection

@push('scripts')
	@can('blog-category-manage')
		<script>
	        editFormByModal('edit-category-modal', 'edit-category-form-append');
		</script>
	@endcan
@endpush
