@extends('frontend.layouts.user.index')
@section('title', __('Withdraw Money'))
@section('content')
    @include('frontend.user.partials._feature_summary_statistics', ['trxType' => \App\Enums\TrxType::WITHDRAW])

    <div class="row">
        @if(!$isWithdrawEnabledToday)
            <div class="container">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>{{ __('Withdrawal Unavailable') }}:</strong>
                        {{ __('Withdrawals are disabled on ') }}
                        {{ implode(', ', $withdrawOffDays) }}.
                        {{ __('Please try again on an enabled day.') }}
                    </div>
                </div>
            </div>
        @endif
        {{-- Withdrawal Form --}}
        <div class="col-lg-12 col-xl-7">
            <div class="single-form-card">
                <div class="card-title mb-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="text-white mb-2 mb-md-0 text-center text-md-start">{{ __('Withdraw Money') }}</h6>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light-success btn-sm"
                           href="{{ route('user.transaction.index', ['type' => \App\Enums\TrxType::WITHDRAW]) }}">
                            <i class="fas fa-list"></i> {{ __('History') }}
                        </a>
                        <a class="btn btn-light-primary btn-sm" href="{{ route('user.withdraw.account.index') }}">
                            <i class="fas fa-university"></i> {{ __('Account') }}
                        </a>
                    </div>
                </div>

                <div class="card-main">
                    <form action="{{ route('user.withdraw.store') }}" method="POST" onsubmit="disableSubmitButton(this, '{{ __('Processing...') }}')">
                        @csrf

                        {{-- Wallet Selector --}}
                        <div class="single-select-inner style-border">
                            <label>{{ __('Withdraw Wallets') }}</label>
                            <select class="form-select wallet-select" name="wallet_id" required>
                                <option disabled>{{ __('Select Wallet') }}</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" @selected($wallet->uuid == request('uuid') || (! request('uuid') && $wallet->id === $defaultWallet->id))>{{ $wallet->name }}
                                        - {{ $wallet->currency->symbol.$wallet->balance }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Withdrawal Account --}}
                        <div class="single-select-inner style-border">
                            <label>{{ __('Withdrawal Account') }}</label>
                            <select class="form-select withdraw-account-select" name="account_id" required>
                                <option disabled selected>{{ __('First Select Wallet') }}</option>
                            </select>
                            <span class="small color-base fw-500 account-info"></span>
                        </div>

                        {{-- Amount Input --}}
                        <div class="single-input-inner style-border mb-0 mt-3">
                            <label class="form-label">{{ __('Withdraw Amount') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control amount-input" name="amount"
                                       placeholder="{{ __('Enter Amount') }}"
                                       oninput="this.value = validateDouble(this.value)" required>
                                <span class="input-group-text">{{ siteCurrency() }}</span>
                            </div>
                            <span class="small color-base fw-500 withdraw-amount-info"></span>
                        </div>


                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-base w-100 mt-4 submit-btn">
                            <x-icon name="check" height="20"/>
                            {{ __('Withdraw Now') }}
                        </button>
                    </form>


                </div>
            </div>
        </div>

        {{-- Summary Section --}}
        @include('frontend.user.withdraw.partials._summary')
    </div>
@endsection
@push('scripts')
    @include('frontend.user.withdraw.partials._script')
@endpush
