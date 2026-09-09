@extends('backend.layouts.app')
@section('title')
    {{ __('Profile Update') }}
@endsection
@section('content')
    <div class="clearfix my-3">
        <div class="fs-3 fw-semibold float-start" >{{ __('Profile Update') }}</div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row">
                <div class="nav flex-column nav-pills  bg-light  rounded px-3 mb-3 mb-md-0 col-12 col-md-4 col-lg-3"
                     id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <header class="header bg-light fixed-header mb-3">
                        {{ __('Profile Menu') }}
                    </header>
                    @foreach($profileSections as $name => $icon)
                        <button class="nav-link text-start mb-2 {{ $name == $activeSection ? 'active' : '' }}"
                                id="v-pills-{{ $name }}-tab"
                                data-coreui-toggle="pill" data-coreui-target="#v-pills-{{ $name }}" type="button" role="tab"
                                aria-controls="v-pills-{{ $name }}"
                                aria-selected="{{ $name == $activeSection ? 'true' : 'false' }}">
                            <x-icon name="{{ $icon }}" height="20" width="20" class="me-2"/> {{ title($name) }}
                        </button>
                    @endforeach
                </div>
                <div class="tab-content rounded col-12 col-md-8 col-lg-9 px-3" id="v-pills-tabContent">
                    @foreach($profileSections as $name => $icon)
                        <div class="tab-pane fade {{ $name == $activeSection ? 'show active' : '' }}" id="v-pills-{{ $name }}" role="tabpanel" aria-labelledby="v-pills-{{ $name }}-tab" tabindex="0">
                            @include('backend.profile.partials._' . $name)
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endSection
