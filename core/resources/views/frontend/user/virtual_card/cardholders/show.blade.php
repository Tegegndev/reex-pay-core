@extends('frontend.layouts.user.index')
@section('title', __('Cardholder Details'))
@section('content')
    <div class="single-form-card">
        <div class="card-title d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <h6 class="text-white mb-2 mb-md-0">{{ __('Cardholder Details') }}</h6>
            <a class="btn btn-light-primary btn-sm" href="{{ route('user.virtual-card.cardholders.index') }}">
                <i class="fa-solid fa-list"></i> {{ __('All Cardholders') }}
            </a>
        </div>
        <div class="card-main">
            <div class="row g-4 mb-3">
                @if($cardholder->card_type->isBusiness())
                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                        <span class="badge badge-sm bg-{{ $cardholder->card_type->class() }} text-white">{{ $cardholder->card_type->label() }}</span>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="mb-2 d-flex align-items-center">
                            <span class="me-2 text-primary">
                                @if($cardholder->status)
                                    <i class="fa-solid fa-circle-check"></i>
                                @else
                                    <i class="fa-solid fa-ban"></i>
                                @endif
                            </span>
                            <span class="fw-semibold">@lang('Status'):</span>
                            <span class="badge text-white bg-{{ $cardholder->status ? 'success' : 'secondary' }} ms-2">
                                {{ $cardholder->status ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                        <div class="fw-bold fs-5 mb-1">{{ $cardholder->full_name ?? ($cardholder->first_name . ' ' . $cardholder->last_name) }}</div>
                        <div class="text-muted small mb-1"><i class="fa-solid fa-envelope me-1"></i>{{ $cardholder->email ?: '-' }}</div>
                        <span class="badge badge-sm bg-{{ $cardholder->card_type->class() }} text-white">{{ $cardholder->card_type->label() }}</span>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="mb-2 d-flex align-items-center">
                            <span class="me-2 text-primary"><i class="fa-solid fa-phone"></i></span>
                            <span class="fw-semibold">@lang('Phone'):</span>
                            <span class="ms-2 text-muted">{{ $cardholder->mobile ?? '-' }}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="me-2 text-primary"><i class="fa-solid fa-flag"></i></span>
                            <span class="fw-semibold">@lang('Country'):</span>
                            <span class="ms-2 text-muted">{{ $cardholder->country ?? '-' }}</span>
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <span class="me-2 text-primary">
                                @if($cardholder->status)
                                    <i class="fa-solid fa-circle-check"></i>
                                @else
                                    <i class="fa-solid fa-ban"></i>
                                @endif
                            </span>
                            <span class="fw-semibold">@lang('Status'):</span>
                            <span class="badge text-white bg-{{ $cardholder->status ? 'success' : 'secondary' }} ms-2">
                                {{ $cardholder->status ? __('Active') : __('Inactive') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            <hr>
            <div class="row g-3 mb-3">
                @if($cardholder->card_type->isBusiness())
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Business Name')</div>
                        <div class="text-muted small">{{ $cardholder->business?->business_name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Business Type')</div>
                        <div class="text-muted small">{{ $cardholder->business?->business_type ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Registration Number')</div>
                        <div class="text-muted small">{{ $cardholder->business?->registration_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('TIN')</div>
                        <div class="text-muted small">{{ $cardholder->business?->tin ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Business Address')</div>
                        <div class="text-muted small">
                            {{ $cardholder->business?->address_line1 }}
                            @if($cardholder->business?->address_line2), {{ $cardholder->business->address_line2 }}@endif
                            {{ $cardholder->business?->city ? ', ' . $cardholder->business->city : '' }}
                            {{ $cardholder->business?->country ? ', ' . $cardholder->business->country : '' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Contact Email')</div>
                        <div class="text-muted small">{{ $cardholder->business?->contact_email ?? '-' }}</div>
                    </div>
                @else
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Date of Birth')</div>
                        <div class="text-muted small">{{ $cardholder->dob ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Address')</div>
                        <div class="text-muted small">
                            {{ $cardholder->address_line1 }}
                            @if($cardholder->address_line2), {{ $cardholder->address_line2 }}@endif
                            {{ $cardholder->city ? ', ' . $cardholder->city : '' }}
                            {{ $cardholder->country ? ', ' . $cardholder->country : '' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Gender')</div>
                        <div class="text-muted small">{{ $cardholder->gender?->label() ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold">@lang('Relation')</div>
                        <div class="text-muted small">{{ $cardholder->relation ?? '-' }}</div>
                    </div>
                @endif
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="fw-semibold">@lang('Created At')</div>
                    <div class="text-muted small">{{ $cardholder->created_at ? $cardholder->created_at->format('d M Y, H:i') : '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="fw-semibold">@lang('Last Updated')</div>
                    <div class="text-muted small">{{ $cardholder->updated_at ? $cardholder->updated_at->format('d M Y, H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection