@extends('backend.layouts.app')
@section('title', __('SEO Management'))
@section('content')
	<div class="d-flex justify-content-between align-items-center my-3">
		<div class="fs-4 fw-semibold">{{ __('SEO Management') }}</div>
		<div>
			<a href="{{ route('admin.page.site.index') }}" class="btn btn-success text-white me-2">
				<x-icon name="page" class="me-1" height="20" width="20"/>
				{{ __('Page Manage') }}
			</a>
			<a href="{{ route('admin.site-seo.create') }}" class="btn btn-primary">
				<x-icon name="add" class="me-1" height="20" width="20"/>
				{{ __('Add SEO') }}
			</a>
		</div>
	</div>
	
	<div class="card border-0 mb-4">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-centered align-items-center">
					<thead class="thead-light">
					<tr>
						<th>{{ __('Page Name') }}</th>
						<th>{{ __('Meta Title') }}</th>
						<th>{{ __('Robots') }}</th>
						<th>{{ __('Image') }}</th>
						<th>{{ __('Updated At') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody>
					@forelse($seos as $seo)
						<tr>
							<td class="text-capitalize">
								
								@if ($seo->isGlobal())
									<div class="d-flex align-items-center gap-1">
										<span>{{ __('Global SEO') }}</span>
										<span class="badge bg-info text-uppercase">{{ __('Primary') }}</span>
									</div>
								@else
									<div class="d-flex align-items-center gap-1">
										<a href="{{ route('admin.page.site.edit',$seo->page_id) }}" class="text-decoration-none">
											<i class="fas fa-up-right-from-square"></i>
											{{ $seo->page->label }}
										</a>
									</div>
								@endif
							
							</td>
							
							<td>{{ $seo->meta_title[app()->getLocale()] ?? '-' }}</td>
							<td>
                            <span class="badge bg-{{ $seo->robots == 'noindex,nofollow' ? 'danger' : 'success' }}">
                                {{ strtoupper($seo->robots) }}
                            </span>
							</td>
							<td>
								@if($seo->image)
									<img src="{{ asset($seo->image) }}" alt="SEO Image" width="50" class="rounded">
								@else
									<span class="text-muted">{{ __('No Image') }}</span>
								@endif
							</td>
							<td>{{ $seo->updated_at->format('d M, Y') }}</td>
							<td>
								<div class="btn-group" role="group" aria-label="Actions">
									<a href="{{ route('admin.site-seo.edit', ['site_seo' => $seo->id]) }}" class="btn btn-primary">
										<x-icon name="manage" height="20" width="20"/> {{ __('Manage SEO') }}
									</a>
									
									{{-- Delete Button (Only if not Global SEO) --}}
									@unless($seo->isGlobal())
										<a href="javascript:void(0)" class="btn btn-danger text-white delete"
										   data-url="{{ route('admin.site-seo.destroy', $seo->id) }}">
											<x-icon name="delete-3" height="20" width="20"/> {{ __('Delete') }}
										</a>
									@endunless
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6">
								<h4 class="text-center text-muted py-3">{{ __('No SEO data available.') }}</h4>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
			
			{{-- Pagination --}}
			<div class="mt-3">
				{{ $seos->links() }}
			</div>
		</div>
	</div>
@endsection
