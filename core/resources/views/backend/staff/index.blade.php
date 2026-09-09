@extends('backend.layouts.app')
@section('title',__('Staffs Management'))
@section('content')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start" >{{ __('Staffs Management') }}</div>
        @can('staff-create')
            <a href="#new_staff_modal" data-coreui-toggle="modal"   class="btn btn-primary float-end"><x-icon name="add"  class="icon" />{{ __('Add New') }}</a>
        @endcan
    </div>

    <div class="card border-0 mb-4">
        <div class="card-body">
            <div class="table-responsive  rounded">
                <table class="table mb-0 caption-top">
                    <thead>
                    <tr>
                        <th>{{ __('Avatar') }}</th>
                        <th>{{ __('Info') }}</th>
                        <th >{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        @can('staff-edit')
                            <th>{{ __('Action') }}</th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($staffs as $staff)
                        <tr class="align-middle">
                            <td>
                                <div class="avatar avatar-md"><img class="avatar-img " src="{{ asset($staff->avatar_alt) }}" height="40" alt="">
                                    <span class="avatar-status bg-{{ $staff->status ? 'success' : 'danger' }}"></span>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap"> {{ title($staff->name) }}</div>
                                <div class="small text-body-secondary text-nowrap">{{ $staff->email }}</div>
                            </td>
                            <td><a class="badge rounded-pill text-bg-info text-white" href="{{ route('admin.role.edit', $staff->roles->first()->id) }}" target="_blank">{{ title($staff->roles->first()->name) }}</a></td>
                            <td>
                                @if($staff->status)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            @can('staff-edit')
                                <td>
                                    <button type="button" data-edit-url="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-primary d-flex align-items-center edit-modal">
                                        <x-icon name="manage" height="20"/>{{ __('Manage') }}
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    {{	$staffs->links() }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create New Staff Modal --}}
    @can('staff-create')
        @include('backend.staff.partials._new_staff')
    @endcan

    {{-- Manage Staff Modal --}}
    @can('staff-edit')
        @include('backend.staff.partials._edit_staff')
    @endcan

@endsection
@push('scripts')
    @can('staff-edit')
        <script>
            $(document).ready(function () {
                editFormByModal('edit_staff_modal','edit_staff_data');
            });
        </script>
    @endcan
@endpush
