@extends('backend.layouts.app')

@section('title', __('Manage HTML'))

@push('styles')
    <link href="{{ asset('backend/css/codemirror.css') }}" rel='stylesheet'>
    <link href="{{ asset('backend/css/ayu-dark.css') }}" rel='stylesheet'>
@endpush

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between w-100 flex-wrap">
            <div class="mb-3 mb-lg-0">
                <h1 class="h4">@lang('Manage HTML for') {{ $landing_page->name }}</h1>
            </div>
            <div class="btn-toolbar mb-md-0 mb-2">
                <a href="{{ route('admin.custom-landing.index') }}" class="btn btn-primary">
                    <x-icon name="back" height="20" width="20" class="me-1"/>
                    @lang('Back')
                </a>
            </div>
        </div>
    </div>
    <div class="alert alert-info">
        <h5>@lang('Instructions')</h5>
        <ul>
            <li>@lang('Use the editor below to modify the HTML content of your landing page.')</li>
            <li>@lang('Ensure all HTML tags are properly closed to avoid rendering issues.')</li>
            <li>@lang('Click "Update HTML" to save changes. Your updates will be reflected immediately.')</li>
            <li>@lang('If you encounter any issues, use the "Back" button to return to the landing page list.')</li>
        </ul>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.custom-landing.manage-html-update', $landing_page->id) }}">
                @csrf
                <div class="form-group mb-4">
                    <label for="htmlContent" class="form-label">@lang('Edit HTML Content')</label>
                    <textarea name="htmlContent" id="htmlContent" class="form-control editorContainer rounded" rows="10">{{ $content }}</textarea>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary" type="submit">
                        <x-icon name="check" height="20" width="20" class="me-2"/>
                        @lang('Update HTML')
                    </button>
                </div>
            </form>
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