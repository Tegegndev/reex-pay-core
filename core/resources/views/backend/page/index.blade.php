@extends('backend.layouts.app')
@section('title', __('Page Manager'))
@section('content')
	<div class="d-flex justify-content-between align-items-center my-3">
		<div class="fs-4 fw-semibold">{{ __('Page Manager') }}</div>
		<div>
			<a href="{{ route('admin.site-seo.index') }}" class="btn btn-success text-white me-2">
				<x-icon name="seo" class="icon"/> {{ __('SEO Manager') }}
			</a>
			@can('page-create')
				<a href="{{ route('admin.page.site.create') }}" class="btn btn-primary">
					<x-icon name="plus" class="icon"/> {{ __('Create Page') }}
				</a>
			@endcan
			
		</div>
	</div>
	<div class="card border-0  mb-4">
		<div class="card-body px-2">
			<div class="table-responsive">
				<table class="table user-table align-items-center">
					<thead class="table-light">
					<tr>
						<th>{{ __('Title') }}</th>
						<th>{{ __('Slug') }}</th>
						<th>{{ __('Component Count') }}</th>
						<th>{{ __('Status') }}</th>
						<th>{{ __('Action') }}</th>
					</tr>
					</thead>
					<tbody>
					@forelse($pages as $page)
						@php
							$seoId = $page->is_home
							   ? \App\Models\SiteSeo::global()?->id
							   : $page->seo?->id;
					   
						    $seoUrl = $seoId
							   ? route('admin.site-seo.edit', $seoId)
							   : route('admin.site-seo.create', ['page_id' => $page->id]);
						@endphp
						<tr>
							<td class="fw-semibold text-start">
								<div class="d-flex align-items-center">
									<div>
										<div class="text-nowrap fw-bold">{{ $page->label }}</div>
										<div class="small text-muted">
											{{ __('Breadcrumb') }}
											@if($page->is_breadcrumb)
												<i class="fa-solid fa-check text-success"></i>
											@else
												<i class="fa-solid fa-xmark text-danger"></i>
											@endif
										</div>
									</div>
								</div>
							</td>
							
							<td class="text-nowrap">
								<a href="{{ url($page->slug === '/' ? '/' : '/' . ltrim($page->slug, '/')) }}" target="_blank"
								   class="fw-semibold text-primary text-decoration-none">
									{{ $page->slug === '/' ? '/' : '/' . ltrim($page->slug, '/') }}
								</a>
							</td>
							
							
							<td>
                                <span class="badge bg-info rounded-pill">
                                    {{ count($page->component_ids) }}
                                </span>
							</td>
							
							<td>
                                <span class="badge bg-{{ $page->is_active ? 'success' : 'danger' }}">
                                    {{ strtoupper($page->is_active ? 'Active' : 'Inactive') }}
                                </span>
							</td>
							
							<td>
								<div class="d-flex">
									@can('page-edit')
											<a href="{{ route('admin.page.site.edit', $page->id) }}" class="btn btn-primary me-2 d-flex align-items-center">
												<x-icon name="manage" height="20" width="20" class="me-1"/> {{ __('Manage') }}
											</a>
									@endcan
									
									<a href="{{ $seoUrl }}" class="btn btn-success me-2 d-flex align-items-center text-white">
										<x-icon name="seo-2" height="20" width="20" class="me-1"/>
										{{ __('SEO') }}
									</a>
									
									@if($page->type === \App\Enums\PageType::Dynamic)
										@can('page-delete')
											<a href="javascript:void(0)" data-url="{{ route('admin.page.site.destroy', $page->id) }}" class="btn  btn-danger text-white d-flex align-items-center delete">
												<x-icon name="delete-2" height="20" width="20" class="me-1"/> {{ __('Delete') }}
											</a>
										@endcan
									@else
										<button disabled class="btn  btn-warning">
											<i class="fa-solid fa-lock me-1"></i> {{ __('Protected') }}
										</button>
									@endif
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="text-center text-muted py-4">
								{{ __('No pages found.') }}
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
