@extends('frontend.layouts.user.index')
@section('title', __('Deposit'))
@section('content')
    @include('frontend.user.partials._feature_summary_statistics', ['trxType' => \App\Enums\TrxType::DEPOSIT])

    <div class="row">
        <div class="col-lg-12 col-xl-7">
            <div class="single-form-card">
                <div class="card-title mb-0 d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="mb-0 text-white">{{ __('Add Money') }}</h6>
                    <a class="btn btn-light-success btn-sm" href="{{ route('user.transaction.index', ['type' => \App\Enums\TrxType::DEPOSIT]) }}">
                        <i class="fas fa-list"></i> {{ __('History') }}
                    </a>
                </div>
                <div class="card-main">
                    <form action="{{ route('user.deposit.store') }}" method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton(this, '{{ __('Processing...') }}')">
                        @csrf
                        <div class="single-select-inner style-border">
                            <label>{{ __('Your Wallets') }}</label>
                            <select class="form-select wallet-select" name="wallet_id" required>
                                <option disabled>{{ __('Select Wallet') }}</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" @selected($wallet->uuid == request('uuid') || (! request('uuid') && $wallet->id === $defaultWallet->id))>{{ __(':name Wallet', ['name' => $wallet->name]) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="single-select-inner style-border mt-3">
                            <label>{{ __('Payment Method') }}</label>
                            <select class="form-select  deposit-method-list" name="payment_method">
                                <option disabled selected>{{ __('First Select Wallet') }}</option>
                            </select>
                            <span class="small color-base fw-500 deposit-method-info span-consistent"></span>
                        </div>

                        <div id="manual-deposit-credentials">

                        </div>

                        <div class="single-input-inner style-border mb-0 mt-3">
                            <label class="form-label">{{ __('Deposit Money') }}</label>
                            <div class="input-group input-group-right">
                                <input type="text" class="form-control deposit-amount" name="amount"
                                       oninput="this.value = validateDouble(this.value)"
                                       placeholder="{{ __('Enter Amount') }}">
                                <span class="input-group-text input-group-text-right">{{ siteCurrency() }}</span>
                            </div>
                            <span class="small color-base fw-500 deposit-amount-info span-consistent"></span>
                        </div>
                        <button type="submit" class="btn btn-base w-100 mt-4 submit-btn">
                            <x-icon name="check" height="20"/>
                            {{ __('Deposit Now') }}</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Summary Section --}}
        @include('frontend.user.deposit.partials._summary')
    </div>
@endsection
@push('scripts')
    @include('frontend.user.deposit.partials._script')
@endpush
