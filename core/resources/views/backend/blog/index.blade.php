@extends('backend.layouts.app')

@section('title', __('Manage Blogs'))

@section('content')
	<div class="py-4 d-flex justify-content-between">
		<h1 class="h4">{{ __('Manage Blogs') }}</h1>
		
	@can('blog-create')
		<a href="{{ route('admin.blog.post.create') }}" class="btn btn-primary">
			<x-icon name="add" height="20" width="20"/> {{ __('Add New Blog') }}
		</a>
	@endcan
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-centered align-middle">
					<thead>
					<tr>
						<th>{{ __('Thumbnail') }}</th>
						<th>{{ __('Title') }}</th>
						<th>{{ __('Category') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody>
					@forelse($blogs as $blog)
						<tr>
							<td>
								<img src="{{ asset($blog->thumbnail) }}" alt="{{ $blog->title_text }}"
								     class="img-fluid rounded avatar" width="100">
							</td>
							<td>{{ $blog->title_text }}</td>
							<td>{{ $blog->category?->name_text ?? '-' }}</td>
							<td>
                                <span class="badge bg-{{ $blog->status ? 'success' : 'danger' }}">
                                    {{ $blog->status ? __('Active') : __('Inactive') }}
                                </span>
							</td>
							<td>
								<div class="btn-group">
									@can('blog-edit')
										<a href="{{ route('admin.blog.post.edit', $blog->id) }}" class="btn btn-primary">
											<x-icon name="edit" height="20" width="20"/> {{ __('Edit') }}
										</a>
									@endcan
									@can('blog-delete')
										<a href="javascript:void(0)" class="btn btn-danger delete text-white"
										   data-url="{{ route('admin.blog.post.destroy', $blog->id) }}">
											<x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
										</a>
									@endcan
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="text-center p-4">{{ __('No blog found') }}</td>
						</tr>
					@endforelse
					</tbody>
				</table>
				
				{{ $blogs->links() }}
			</div>
		</div>
	</div>

@endsection
