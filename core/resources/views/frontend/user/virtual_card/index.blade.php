@php use App\Models\PaymentGateway; @endphp
@extends('frontend.layouts.user.index')
@section('title', __('My Virtual Cards'))
@section('content')
    <div class="single-form-card">
        <div class="card-title d-flex flex-column flex-md-row justify-content-between">
            <h6 class="text-white mb-2 mb-md-0">{{ __('My Virtual Cards') }}</h6>
            <div class="d-flex gap-2 flex-row">
                <a class="btn btn-light-secondary btn-sm" href="{{ route('user.virtual-card.cardholders.index') }}">
                    <i class="fa-solid fa-users"></i> {{ __('Cardholders') }}
                </a>
                <a class="btn btn-light-primary btn-sm" href="{{ route('user.virtual-card.request.index') }}"><i class="fa-solid fa-list"></i>{{ __('My Requests') }}</a>
                <button type="button" class="btn btn-light-success btn-sm" data-bs-toggle="modal" data-bs-target="#requestVirtualCardModal"><i
                        class="fa-solid fa-credit-card me-2"></i>{{ __('Request New') }}</button>
            </div>
        </div>
        <div class="card-main">
            @if($cards->count())
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach($cards as $card)
                        @php
                            $isVisa = Str::lower($card->brand) === 'visa';
                            $providerName = $card->provider->code;
                        @endphp
                        <div class="col">
                            <div class="credit-card-mockup position-relative rounded-4 shadow-lg p-4 mb-3 text-white {{ $isVisa ? 'visa-pro-card' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-bold fs-4 ls-2 d-flex align-items-center gap-2">
                                        <x-icon name="nfc" class="text-info" height="50"/>
                                    </span>
                                    <span>
                                        <x-icon name="{{ Str::lower($card->brand) }}" class="ms-2" height="50"/>
                                    </span>
                                </div>
                                <div class="mb-3 d-flex align-items-center gap-2 justify-content-between position-relative">
                                    <span class="fs-3 ls-3 card-number-group mb-0" id="masked-{{ $card->id }}">
                                        •••• •••• •••• <b>{{ $card->last4 }}</b>
                                    </span>
                                    <span class="fs-3 ls-3 card-number-group mb-0 d-none" id="full-demo-{{ $card->id }}">
                                        4111 1111 1111 {{ str_pad($card->last4, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="ms-2 d-flex align-items-center">
                                        @if($demoMode)
                                            <a href="javascript:void(0);" class="text-white demo-toggle-details d-flex align-items-center"
                                               style="font-size:1.3rem;" data-cardid="{{ $card->id }}" title="{{ __('Show/Hide Card Details') }}">
                                                <i class="fa-solid fa-eye-slash show-hide-icon" id="eye-icon-{{ $card->id }}"></i>
                                            </a>
                                        @else
                                            @if(!empty($card->meta['card_id']))
                                                <button type="button"
                                                        class="btn btn-primary show-card-details"
                                                        data-card-id="{{ $card->meta['card_id'] }}"
                                                        data-provider="{{ $providerName }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cardDetailsModal"
                                                        aria-label="{{ __('Show Card Details') }}">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-secondary fw-bold">{{ __('Expiry') }}</div>
                                        <div>{{ $card->expiry_month }}/{{ $card->expiry_year }}</div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 small">
                                    <span class="text-white-50">
                                        {{ $card->wallet->currency->name ?? '' }} ({{ $card->wallet->currency->code ?? '' }})
                                    </span>
                                    <span class="d-flex align-items-center gap-2">
                                        <a href="{{ route('user.virtual-card.topup', $card) }}" class="btn btn-light btn-sm p-2 d-flex align-items-center justify-content-center rounded-circle shadow-sm me-1 topup-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Top Up">
                                            <i class="fa-solid fa-arrow-down text-primary fs-5"></i>
                                        </a>
                                        <a href="{{ route('user.virtual-card.withdraw', $card) }}" class="btn btn-light btn-sm p-2 d-flex align-items-center justify-content-center rounded-circle shadow-sm withdraw-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Withdraw">
                                            <i class="fa-solid fa-arrow-up text-success fs-5"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light text-muted text-center my-4">
                    {{ __('No virtual cards found.') }}
                </div>
            @endif
        </div>
    </div>
    
    {{-- Card Details Modal --}}
    @if(!$demoMode)
        @include('frontend.user.virtual_card.partials._card_details_modal')
    @endif
    
    @include('frontend.user.virtual_card.request.partials._add_card_request_modal')
@endsection

@push('scripts')
    @include('frontend.user.virtual_card.partials._script')
@endpush