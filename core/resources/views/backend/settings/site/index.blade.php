@extends('backend.settings.index')
@section('setting_title', __('Site Settings'))
@section('setting_content')
    @php($activeSection = session('section') ?? 'general_settings')
    <div class="d-flex flex-column flex-md-row">
        <div class="nav flex-column nav-pills me-3 bg-light  rounded px-3 mb-3 mb-md-0 col-12 col-md-4 col-lg-3"
             id="v-pills-tab" role="tablist" aria-orientation="vertical">
	        <header class="header bg-light fixed-header mb-3">
                {{ __('Settings Menu') }}
            </header>
            @foreach($settingMenus as $name => $icon)
                <button class="nav-link text-start mb-2 {{ $activeSection === $name ? 'active' : '' }}"
                        id="v-pills-{{ $name }}-tab"
                        data-coreui-toggle="pill" data-coreui-target="#v-pills-{{ $name }}" type="button" role="tab"
                        aria-controls="v-pills-{{ $name }}"
                        aria-selected="{{ $activeSection === $name ? 'true' : 'false' }}">
                    <x-icon name="{{ $icon }}" height="20" width="20" class="me-2"/> {{ title($name) }}
                </button>
            @endforeach
        </div>
        <div class="tab-content rounded col-12 col-md-8 col-lg-9 px-3" id="v-pills-tabContent">
            @foreach($settings as $section => $fields)
		        
		        <div class="tab-pane fade {{ $activeSection === $section ? 'show active' : '' }}"
                     id="v-pills-{{ $section }}" role="tabpanel" aria-labelledby="v-pills-{{ $section }}-tab"
                     tabindex="0">
                    <div class="card">
	                    <div class="card-header d-flex justify-content-between align-items-center fixed-header bg-light py-2 px-3">
		                    <h5 class="mb-0 text-dark">{{ title($section) }}</h5>
		                    
		                    @if (isset($fields['include_partials']))
			                    @include('backend.settings.site.partials.' . $fields['include_partials'],['section' => $section])
		                    @endif
	                    </div>
	                    
	                    <div id="errorAlert-{{$section}}" class="alert alert-danger mt-3  d-none" role="alert"></div>
	                    
	                    @if (isset($fields['info']))
		                    <div class="alert alert-info border-0 rounded-2 mb-2 mt-3" style="background: #eef6fb; color: #055160; font-size: 14px;">
			                    <div class="title">
				                    ⚡ {{ __('Important') }}
			                    </div>
			                    <div class="description">
				                    {{ $fields['info'] }}
			                    </div>
		                    </div>
	                    @endif
                        
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.settings.site.update', $section) }}"
                                  enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    @foreach($fields['elements'] as $key => $field)
                                        @if($field['type'] !== 'hidden')
                                            <div class="mb-3 {{ $field['class'] }}">
                                                @include('backend.settings.site.partials.fields.' . $field['type'], ['field' => $field])
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="col-12">
                                    {{-- Submit --}}
                                    <x-form.submit-button icon="check">
                                        {{ __('Save Changes') }}
                                    </x-form.submit-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
@push('scripts')
   @include('backend.settings.site.partials._script')
@endpush
