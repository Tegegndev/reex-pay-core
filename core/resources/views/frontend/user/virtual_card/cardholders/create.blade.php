@extends('frontend.layouts.user.index')
@section('title', __('Add Cardholder'))
@section('content')
    <div class="single-form-card">
        <div class="card-title d-flex flex-column flex-md-row justify-content-between">
            <h6 class="text-white mb-2 mb-md-0">{{ __('Add Cardholder') }}</h6>
            <a class="btn btn-light-primary btn-sm" href="{{ route('user.virtual-card.cardholders.index') }}">
                <i class="fa-solid fa-list"></i> {{ __('All Cardholders') }}
            </a>
        </div>
        <div class="card-main">
            <form method="POST" action="{{ route('user.virtual-card.cardholders.store') }}" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="card_type" class="form-label">@lang('Cardholder Type')</label>
                        <select class="form-select" id="card_type" name="card_type">
                            <option value="">@lang('Select Cardholder Type')</option>
                            @foreach($cardholderType as $type)
                                <option value="{{ $type->value }}" {{ old('card_type','personal')==$type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 kyc-fields-wrap">
                        <label for="kyc_template_id" class="form-label fw-semibold text-primary-emphasis">@lang('KYC Type')</label>
                        <select class="form-select" id="kyc_template_id" name="kyc_template_id">
                            <option value="">@lang('Select KYC Type')</option>
                            @if(isset($kycTemplates))
                                @foreach($kycTemplates as $tpl)
                                    <option value="{{ $tpl->id }}">{{ $tpl->title }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div id="kyc-fields-dynamic"></div>
                <!-- Personal Details-->
                <div id="personal-details-block">
                    @include('frontend.user.virtual_card.cardholders.partials._personal_details')
                </div>
                <!-- Business Details-->
                <div id="business-details-block" style="display:none">
                    @include('frontend.user.virtual_card.cardholders.partials._business_details')
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('user.virtual-card.cardholders.index') }}" class="btn btn-secondary me-2">
                        <x-icon name="x" class="me-1" height="20" width="20"/>
                        @lang('Cancel')
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <x-icon name="check" class="me-1" height="20" width="20"/>
                        @lang('Save Cardholder')
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    @include('frontend.user.virtual_card.cardholders.partials._script')
    <script>
        "use strict";
        $(function() {
            function toggleFields() {
                var type = $('#card_type').val();
                if(type === 'business') {
                    $('#personal-details-block').hide();
                    $('#kyc_template_id').closest('.kyc-fields-wrap').hide();
                    $('#business-details-block').show();
                } else {
                    $('#personal-details-block').show();
                    $('#kyc_template_id').closest('.kyc-fields-wrap').show();
                    $('#business-details-block').hide();
                }
            }
            $('#card_type').on('change', toggleFields);
            toggleFields();
        });
    </script>
@endpush