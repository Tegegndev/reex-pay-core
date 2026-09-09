@extends('backend.layouts.app')
@section('title', __('Edit Page'))
@section('content')
	<div class="container-fluid py-4">
		<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
			<h1 class="h4">{{ __('Edit Page') }}</h1>
			<a href="{{ route('admin.page.site.index') }}" class="btn btn-primary d-inline-flex align-items-center">
				<x-icon name="back" class="me-1"/>
				{{ __('Back') }}
			</a>
		</div>
		
		<div class="row g-4">
			<div class="col-12 col-xl-6">
				@include('backend.page.partials._components_list', ['components' => $components, 'selected' => $page->component_ids])
			</div>
			<div class="col-12 col-xl-6">
				@include('backend.page.partials._components_form_edit', ['page' => $page])
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	@include('backend.page.partials._component_script')
@endpush