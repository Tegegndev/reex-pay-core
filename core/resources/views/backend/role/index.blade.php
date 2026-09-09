@extends('backend.layouts.app')
@section('title',__('Roles'))
@section('content')


    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start" >{{ __('Roles & Permissions') }}</div>
        <a href="{{ route('admin.role.create') }}"  class="btn btn-primary float-end"><x-icon name="add"  class="icon" />{{ __('Add New') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 caption-top">
                    <thead>
                    <tr>
                        <th>{{ __('Assigned Staffs') }}</th>
                        <th>{{ __('Role Name') }}</th>
                        <th>{{ __('Role Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr class="align-middle">
                            <td>
                                <div class="avatars-stack d-flex align-items-center flex-wrap gap-1">
                                    @if($role->users->count())
                                        @foreach($role->users->take(3) as $user)
                                            <div class="avatar avatar-md border rounded-circle overflow-hidden" data-coreui-popper=""
                                                 data-coreui-toggle="tooltip"
                                                 data-coreui-title="{{ $user->name ?? 'User' }}">
                                                <img class="avatar-img rounded-circle" src="{{ asset($user->avatar_alt) }}" alt="{{ $user->name ?? 'User' }}">
                                            </div>
                                        @endforeach
                                        
                                        @if($role->users->count() > 3)
                                            <div class="avatar avatar-md bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle fw-semibold">
                                                +{{ $role->users->count() - 3 }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-muted small d-flex align-items-center">
                                            <i class="fa-regular fa-user me-1"></i>
                                            {{ __('No Staff Assigned') }}
                                        </div>
                                    @endif
                                </div>
                            
                            </td>
                            <td>
                                <div class="text-nowrap"> {{ title($role->name) }}</div>
                                <div class="small text-body-secondary text-nowrap">
                                    {{ $role->description }}
                                </div>
                            </td>
                            <td>
                               <div class="d-inline-flex">
                                   @if($role->name != 'super-admin')
                                       @can('role-edit')
                                           <a href="{{ route('admin.role.edit', $role->id) }}" class="btn btn-primary text-white manage-plugin me-1"><x-icon height="24" width="24" name="manage"/>{{ __('Manage') }}</a>
                                       @endcan
                                       @can('role-delete')
                                           @if(auth()->user()->roles->first()->name != $role->name)
                                               <a href="#" class="btn btn-danger text-white delete" data-url="{{ route('admin.role.destroy', $role->id) }}">
                                                   <x-icon height="24" width="24" name="delete-2"/> {{ __('Delete') }}
                                               </a>
                                           @endif
                                       @endcan
                                   @else
                                       <button disabled class="btn  btn-warning">
                                           <i class="fa-solid fa-lock me-1"></i> {{ __('Protected') }}
                                       </button>
                                   @endif
                               </div>

                            </td>
                        </tr>
                    @endforeach
                    {{	$roles->links() }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
