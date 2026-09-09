@extends('backend.layouts.app')
@section('title',__('Role Create'))
@section('content')


<div class="clearfix my-3">
    <div class="fs-3 fw-semibold float-start" >{{ __('Create Role') }}</div>
    <a href="{{ route('admin.role.index') }}"  class="btn btn-primary float-end"><x-icon name="back"  class="icon" />{{ __('Back') }}</a>
</div>
<div class="card p-4">
    <form action="{{ route('admin.role.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-3 col-md-4 mb-3">
                <label class="form-label">{{ __('Role Name') }}</label>
                <input type="text" name="role_name" class="form-control" placeholder="{{ __('Role Name') }}">
            </div>
            <div class="col-lg-9 col-md-8 mb-3">
                <label class="form-label">{{ __('Role Description') }}</label>
                <input type="text" name="description" class="form-control" placeholder="{{ __('Role Description') }}">
            </div>
        </div>
        <div class="row">
            <label class="form-label">#{{ __('All Permissions') }}</label>
            <div class="col-lg-3 col-md-4 mb-3">
                <!-- Heading stays fixed -->
                <div class="bg-light rounded px-3 py-2 border mb-2 fw-semibold text-muted">
                    {{ __('Role Category') }}
                </div>
                
                <!-- Scrollable Role Categories -->
                <div class="scrollable-nav px-2 pt-3 pb-2 border rounded bg-light">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        @foreach(array_keys($permissions->toArray()) as $category)
                            <button class="nav-link text-start mb-2 d-flex align-items-center {{ $loop->first ? 'active' : '' }}"
                                    id="v-pills-{{ $category }}-tab"
                                    data-coreui-toggle="pill"
                                    data-coreui-target="#v-pills-{{ $category }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="v-pills-{{ $category }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <x-icon :name="$category" height="18" width="18" class="me-2" />
                                <span>{{ title($category) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>


            <div class="col-lg-9 col-md-8 d-flex flex-column">
                <div class="tab-content border rounded flex-grow-1 h-100" id="v-pills-tabContent">
                    @foreach($permissions as $category => $permissionList)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="v-pills-{{ $category }}" role="tabpanel" aria-labelledby="v-pills-{{ $category }}-tab">
                            <div class="card h-100 d-flex flex-column">
                                <div class="card-header bg-light">{{ title($category) }}</div>
                                <div class="card-body flex-grow-1">
                                    <div class="row">
                                        @foreach($permissionList as $permission)
                                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                                                <div class="form-check form-switch card-body border rounded  d-flex align-items-center">
                                                    <label class="form-check-label flex-grow-1" for="permission-{{ $permission->id }}">{{ title($permission->name) }}</label>
                                                    <input type="hidden" name="permission[{{ $permission->id }}]" value="0">
                                                    <input class="form-check-input coevs-switch flex-shrink-0 ms-2 " type="checkbox" role="switch" name="permission[{{ $permission->id }}]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}">
                                                </div>

                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary mt-3">
                        <x-icon name="check" height="20"/> {{ __('Create Role') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection
