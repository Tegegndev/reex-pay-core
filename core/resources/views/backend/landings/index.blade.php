@extends('backend.layouts.app')
@section('title', __('All Landing Pages'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">@lang('Landing Pages')</h5>
    <a href="{{ route('admin.custom-landing.create') }}" class="btn btn-primary">
      <x-icon name="plus" class="icon"/> @lang('Add New Landing Page')
    </a>
  </div>
  <div class="card-body">
    <div class="table-responsive  rounded">
      <table class="table mb-0 caption-top">
        <thead>
        <tr>
          <th scope="col">@lang('Name')</th>
          <th scope="col">@lang('Status')</th>
          <th scope="col">@lang('Folder')</th>
          <th scope="col">@lang('Actions')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($landings as $landing)
          <tr>
            <td>{{ $landing->name }}</td>
            <td>
              @if($landing->status)
                <span class="badge bg-success text-uppercase">{{ __('Active') }}</span>
              @else
                <span class="badge bg-danger text-uppercase">{{ __('Inactive') }}</span>
              @endif
            </td>
            <td>{{ $landing->folder }}</td>
            <td>
              <div class="btn-group" role="group" aria-label="Actions">
                <a href="{{ route('admin.custom-landing.manage-html', $landing->id) }}" class="btn btn-secondary">
                  <x-icon name="html" height="20" width="20"/>{{ __('Manage HTML') }}
                </a>
                <a href="{{ route('admin.custom-landing.edit', $landing) }}" class="btn btn-primary">
                  <x-icon name="manage" height="20" width="20"/>{{ __('Edit') }}
                </a>
                <a href="javascript:void(0)" class="btn btn-danger text-white delete"
                   data-url="{{ route('admin.custom-landing.destroy', $landing) }}">
                  <x-icon name="delete-3" height="20" width="20"/>{{ __('Delete') }}
                </a>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
