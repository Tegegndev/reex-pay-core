@extends('backend.layouts.app')
@section('title', 'Referral Card Content Management')

@section('content')
    <div class="container-fluid">
        <div class="clearfix my-4 mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-0">{{ __('Referral Card Content') }}</h2>
                    <p class="text-muted mb-0 small">{{ __('Manage referral card heading, guidelines and image for all languages.') }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.referral.index') }}" class="btn btn-secondary d-flex align-items-center">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        <span class="d-none d-sm-inline">{{ __('Back to Referral') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="row bg-white g-3 rounded-2 p-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.referral.content.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Referral Card Image') }}</label>
                                    <x-img name="referral_card_image_file" :old="$referralContent->image_path"/>
                                </div>
                            </div>
                            
                            <!-- Multi-Language Tabs for Content -->
                            <div class="row">
                                <div class="col-12">
                                    @if(!empty($availableLocales))
                                        <!-- Language Tabs -->
                                        <ul class="nav nav-tabs" id="languageTabs" role="tablist">
                                            @foreach($availableLocales as $code => $name)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                       id="{{ $code }}-tab"
                                                       href="#{{ $code }}-content"
                                                       role="tab"
                                                       onclick="showTab('{{ $code }}')">
                                                        {{ $name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        
                                        <!-- Tab Content -->
                                        <div class="tab-content mt-3" id="languageTabContent">
                                            @foreach($availableLocales as $code => $name)
                                                <div class="tab-pane {{ $loop->first ? 'show active' : '' }}"
                                                     id="{{ $code }}-content"
                                                     role="tabpanel">
                                                    
                                                    <!-- Heading Input -->
                                                    <div class="mb-4">
                                                        <label for="heading_{{ $code }}" class="form-label">
                                                            <i class="fa-solid fa-heading me-1"></i>{{ __('Referral Card Heading') }} ({{ $name }})
                                                        </label>
                                                        <input type="text"
                                                               class="form-control @error('heading.'.$code) is-invalid @enderror"
                                                               id="heading_{{ $code }}"
                                                               name="heading[{{ $code }}]"
                                                               value="{{ old('heading.'.$code, is_array($referralContent->heading) ? ($referralContent->heading[$code] ?? '') : ($code === 'en' ? $referralContent->heading : '')) }}"
                                                               placeholder="{{ __('Enter referral card heading in') }} {{ $name }}"
                                                            {{ $code === 'en' ? 'required' : '' }}>
                                                        @error('heading.'.$code)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <!-- Positive Guidelines -->
                                                    <div class="mb-4">
                                                        <label class="form-label">
                                                            <i class="fa-solid fa-circle-check text-success me-1"></i>{{ __('Positive Guidelines') }} ({{ $name }})
                                                        </label>
                                                        <div id="positive-guidelines-{{ $code }}">
                                                            @php
                                                                $positiveGuidelines = old('positive_guidelines.'.$code, []);
                                                                if (empty($positiveGuidelines)) {
                                                                    if (is_array($referralContent->positive_guidelines) && isset($referralContent->positive_guidelines[$code])) {
                                                                        $positiveGuidelines = $referralContent->positive_guidelines[$code];
                                                                    } elseif ($code === 'en' && is_array($referralContent->positive_guidelines)) {
                                                                        $positiveGuidelines = $referralContent->positive_guidelines;
                                                                    }
                                                                }
                                                                
                                                                if (empty($positiveGuidelines)) {
                                                                    $positiveGuidelines = [''];
                                                                }
                                                            @endphp
                                                            
                                                            @foreach($positiveGuidelines as $index => $guideline)
                                                                <div class="input-group mb-2 positive-guideline-group">
                                                                    <input type="text"
                                                                           class="form-control"
                                                                           name="positive_guidelines[{{ $code }}][]"
                                                                           value="{{ $guideline }}"
                                                                           placeholder="{{ __('Enter positive guideline in') }} {{ $name }}">
                                                                    <button class="btn btn-danger text-white remove-guideline" type="button">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-success btn-sm text-white add-positive-guideline" data-lang="{{ $code }}">
                                                            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Positive Guideline') }}
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Negative Guidelines -->
                                                    <div class="mb-4">
                                                        <label class="form-label">
                                                            <i class="fa-solid fa-circle-xmark text-danger me-1"></i>{{ __('Negative Guidelines') }} ({{ $name }})
                                                        </label>
                                                        <div id="negative-guidelines-{{ $code }}">
                                                            @php
                                                                $negativeGuidelines = old('negative_guidelines.'.$code, []);
                                                                if (empty($negativeGuidelines)) {
                                                                    if (is_array($referralContent->negative_guidelines) && isset($referralContent->negative_guidelines[$code])) {
                                                                        $negativeGuidelines = $referralContent->negative_guidelines[$code];
                                                                    } elseif ($code === 'en' && is_array($referralContent->negative_guidelines)) {
                                                                        $negativeGuidelines = $referralContent->negative_guidelines;
                                                                    }
                                                                }
                                                                
                                                                if (empty($negativeGuidelines)) {
                                                                    $negativeGuidelines = [''];
                                                                }
                                                            @endphp
                                                            
                                                            @foreach($negativeGuidelines as $index => $guideline)
                                                                <div class="input-group mb-2 negative-guideline-group">
                                                                    <input type="text"
                                                                           class="form-control"
                                                                           name="negative_guidelines[{{ $code }}][]"
                                                                           value="{{ $guideline }}"
                                                                           placeholder="{{ __('Enter negative guideline in') }} {{ $name }}">
                                                                    <button class="btn btn-danger text-white remove-guideline" type="button">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-danger text-white btn-sm add-negative-guideline" data-lang="{{ $code }}">
                                                            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Negative Guideline') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            {{ __('No active languages found. Please activate languages in the language management section.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            
                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-save me-2"></i>{{ __('Save Referral Content') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
"use strict";

$(document).ready(function() {
    // CoreUI tab system (no Bootstrap dependency)
    window.showTab = function(tabId) {
        // Hide all tab panes
        $('.tab-pane').removeClass('show active');
        $('.nav-link').removeClass('active');
        
        // Show selected tab pane
        $('#' + tabId + '-content').addClass('show active');
        $('#' + tabId + '-tab').addClass('active');
    };
    
    // Add positive guideline
    $(document).on('click', '.add-positive-guideline', function() {
        const lang = $(this).data('lang');
        const container = $(`#positive-guidelines-${lang}`);
        const newGuideline = `
            <div class="input-group mb-2 positive-guideline-group">
                <input type="text" 
                       class="form-control" 
                       name="positive_guidelines[${lang}][]" 
                       placeholder="{{ __('Enter positive guideline') }}">
                <button class="btn btn-danger text-white remove-guideline" type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        container.append(newGuideline);
    });
    
    // Add negative guideline
    $(document).on('click', '.add-negative-guideline', function() {
        const lang = $(this).data('lang');
        const container = $(`#negative-guidelines-${lang}`);
        const newGuideline = `
            <div class="input-group mb-2 negative-guideline-group">
                <input type="text" 
                       class="form-control" 
                       name="negative_guidelines[${lang}][]" 
                       placeholder="{{ __('Enter negative guideline') }}">
                <button class="btn btn-danger text-white remove-guideline" type="button">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        container.append(newGuideline);
    });
    
    // Remove guideline
    $(document).on('click', '.remove-guideline', function() {
        $(this).closest('.input-group').remove();
    });
});
</script>

@endpush
