@extends('backend.layouts.app')
@section('title', __('Languages'))
@section('content')
	<div class="py-4">
		<div class="d-flex justify-content-between w-100 flex-wrap">
			<div class="mb-3 mb-lg-0">
				<h1 class="h4">{{  __('Languages Manage') }}</h1>
			</div>
			@can('language-manage')
				<div class="btn-toolbar  mb-md-0 mb-2 ">
					<a  href="{{ route('admin.language.sync-missing-keys') }}" class="btn btn-primary d-inline-flex align-items-center me-2">
						<x-icon name="sync" height="20" width="20" class="me-1" />
						{{ __('Sync Missing Translation') }}
					</a>
						<button type="button" class="btn btn-primary d-inline-flex align-items-center" data-coreui-toggle="modal" data-coreui-target="#new-lang-modal">
							<x-icon name="add" height="20" width="20" class="me-1"/>
							{{ __('Add New') }}
						</button>
				</div>
			@endcan

		</div>
	</div>
	<div class="card border-0 mb-4">

		<div class="card-body">
            <div class="table-responsive">
                <table class="table user-table align-items-center">
                    <thead>
                    <tr>
                        <th>{{ __('Flag') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Default') }}</th>
                        <th>{{ __('Status') }}</th>
	                    @can('language-manage')
	                        <th>{{ __('Action') }}</th>
	                    @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($languages as $language)
                        <tr class="align-middle">
                            <td>
                                <img class="icon icon-xl" src="{{ asset($language->flag) }}" alt="">
                            </td>
                            <td>{{ $language->name }}</td>
                            <td>{{ $language->code }}</td>
                            <td>
                                @if ($language->is_default)
                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($language->status)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
	                        @can('language-manage')
	                            <td>
	                                <div class="d-inline-flex">
	                                    <a href="{{ route('admin.language.translate', $language->code) }}"  class="btn btn-info btn-sm text-white me-1">
	                                        <x-icon name="translate" class="icon"/>{{ __('Translate') }}
	                                    </a>
	                                    <button  data-edit-url="{{ route('admin.language.edit', $language->id) }}" class="edit-modal btn btn-primary btn-sm me-1">
		                                    <x-icon name="manage" class="icon"/>{{ __('Manage') }}
	                                    </button>
	                                    @if($language->code != 'en')
	                                        <button data-id="{{ $language->id }}" data-url="{{ route('admin.language.destroy', $language->id) }}" class="delete btn btn-danger btn-sm text-white">
	                                            <x-icon name="delete-3" class="icon"/>{{ __('Delete') }}
	                                        </button>
	                                    @endif
	                                </div>
	                            </td>
	                        @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('No Data Available') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
	</div>
	
	@can('language-manage')
		
		{{-- New Lang Modal --}}
		@include('backend.languages.partial._new_lang_modal')

		{{-- Edit Lang Modal --}}
		@include('backend.languages.partial._edit_lang_modal')
		
	@endcan
@endsection
@push('scripts')
	@can('language-manage')
		<script>
            $(document).ready(function () {
                editFormByModal('edit-lang-modal','edit-lang-append');
            });
		</script>
	@endcan
@endpush
