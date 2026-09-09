@extends('backend.layouts.app')
@section('title', __('Languages Translation'))
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{  __('Languages Translation') }}</h1>
			</div>
			<div class="btn-toolbar  mb-md-0 mb-2 ">
				<a href="{{ route('admin.language.index') }}" class="btn btn-primary d-inline-flex align-items-center me-2">
					<x-icon name="back" height="20" width="20" class="me-1"/>
					{{ __('Back') }}
				</a>
			</div>
		</div>
	</div>

	<div class="card border-0 mb-4">

		<div class="card-header">
			<form action="{{ route('admin.language.translate',  $languageCode ) }}" method="get">
				<div class="row align-items-end">
					<div class="col-lg-3">
						<div class="input-group">
							<input type="text" class="form-control" name="filter" value="{{ request()->get('filter') }}" placeholder="Search" aria-label="Search">
							<button class="input-group-text" type="submit">
								<x-icon name="search-2" height="24" width="24"/>
							</button>
						</div>
					</div>
					<div class="col-lg-9 col-md-6 d-flex justify-content-end">
						<div class="col-md-2">
							@include('backend.languages.partial._select', ['name' => 'language', 'items' => $languages, 'selected' => $languageCode, 'submit' => true])
						</div>
						&nbsp;&nbsp;
						<div class="col-md-2">
							@include('backend.languages.partial._select', ['name' => 'group', 'items' => $groups, 'selected' => request()->get('group'), 'optional' => true, 'submit' => true])
						</div>
					</div>
				</div>
			</form>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-xl-12">
					<div class="table-responsive">
						@isset($translations)

							<table class="table user-table align-items-center ">
								<thead class="bg-light">
								<tr>
									<th>{{ __('GROUP / SINGLE') }}</th>
									<th>{{ __('KEY') }}</th>
									<th>{{ config('app.default_language') }}</th>
									<th>{{$languageCode}}</th>
									<th>{{ __('Action') }}</th>
								</tr>
								</thead>
								<tbody>
								@include('backend.languages.translate_list')
								</tbody>
							</table>

						@endisset
					</div>
				</div>
			</div>
		</div>
	</div>

    @include('backend.languages.partial._edit_translate_key')

@endsection
@push('scripts')

	<script>
        (function ($) {
            "use strict";
            $('.editKeyword').on('click', function (e) {
                var $this = $(this);
                var settings = {
                    key: $this.data('key'),
                    value: $this.data('value'),
                    group: $this.data('group'),
                    language: $this.data('language')
                };

                $('.key-label').text('Key: ' + settings.key);
                $('.key-key').val(settings.key);
                $('.key-value').val(settings.value);
                $('.key-group').val(settings.group);
                $('.key-language').val(settings.language);
                $('#updateTranslate').modal('show');
            });
        })(jQuery);
	</script>
@endpush
