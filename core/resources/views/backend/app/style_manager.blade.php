@extends('backend.layouts.app')

@section('title', __('Custom Style Manager'))

@push('styles')
	<link href="{{ asset('backend/css/codemirror.css') }}" rel='stylesheet'>
	<link href="{{ asset('backend/css/ayu-dark.css') }}" rel='stylesheet'>
@endpush

@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{ __('Custom Style Manager') }}</h1>
			</div>
			<div class="btn-toolbar  mb-md-0 mb-2 ">
				<a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
					<x-icon name="back" height="20" width="20" class="me-1"/>
					{{ __('Back') }}
				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-xl-12">
			<div class="card card-body border-0 mb-4">
				<div class="alert alert-info border-0 rounded-2 mb-2 " style="background: #eef6fb; color: #055160; font-size: 14px;">
					<div class="title">
						⚡ {{ __('Important') }}
					</div>
					<div class="description">
						{{ __('Add your own styles to personalize the look and feel of your site. These styles will override the default design') }}
					</div>
				</div>
				
				<form method="POST" action="{{ route('admin.app.style-manager-update') }}">
					@csrf
					
					<input type="hidden" name="type" value="{{ $css->type }}">
					
					<div class="row mt-2">
						<div class="col-md-12 mb-3">
							<label for="css" class="form-label">{{ __('Write your custom CSS') }}</label>
							<textarea name="content" id="css" class="form-control editorContainer rounded" rows="10">{{ $css->content }}</textarea>
						</div>
					</div>
					<div class="mb-3">
						<label class="form-check-label" for="status">{{ __('Active') }}</label>
						<div class="form-check form-switch">
							<input class="form-check-input coevs-switch" type="checkbox" name="status" id="status" value="1" @checked($css->status)>
						</div>
					</div>
					<div class="mt-3 text-end">
						<button class="btn btn-primary" type="submit">
							<x-icon name="check" height="20"/>
							{{ __('Update Now') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('backend/js/codemirror.js') }}"></script>
	<script src="{{ asset('backend/js/code-css.js') }}"></script>
	<script>
        (() => {
            'use strict';
            var editorContainer = document.querySelector('.editorContainer')

            var editor = CodeMirror.fromTextArea(editorContainer, {
                lineNumbers: true,
                mode: 'css',
                theme: 'ayu-dark',
            });
        })();
	</script>
@endpush
