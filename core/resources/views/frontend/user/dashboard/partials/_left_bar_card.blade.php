@php use App\Constants\WalletAssets; @endphp
@php($ugxWallets = auth()->user()->activeWallets()->filter(fn ($wallet) => $wallet->currency->code === 'UGX'))
<div class="single-card-box single-card-box-slider mb-30 @if(!request()->routeIs('user.dashboard'))  d-lg-block d-none @endif">
    <h6 class="main-title text-white-small">{{ __('Wallets') }}</h6>

    <div class="walet-slider owl-carousel mb-20">
        @foreach($ugxWallets as $wallet)
        <div class="walet-inner">
            <img class="bg-img" src="{{ WalletAssets::getRandomBackground() }}" alt="img">
            <h6 class="sim-card text-white">
                <img src="{{ asset(WalletAssets::WALLET_CHIP) }}" alt="img">
                <span>{{ $wallet->currency->code }}</span>
            </h6>
            <h6 class="title text-white"> {{ __('W/ID') }}: {{ implode(' ', str_split($wallet->uuid, 3)) }}
                <i class="fa-solid fa-copy  cursor-pointer copy-icon copyNow"
                   data-clipboard-text="{{ $wallet->uuid }}"
                   title="{{ __('Copy Wallet ID') }}"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"></i>
            </h6>
            <h5 class="amount text-white">
                {{ $wallet->currency->symbol }}{{ number_format($wallet->balance, 2) }}
            </h5>
        </div>
        @endforeach
    </div>

    <div class="btn-wrap d-flex w-100 gap-3">
        <a class="btn btn-base d-flex align-items-center justify-content-center w-50"
           href="{{ route('user.deposit.create') }}">
            <x-icon name="deposit" height="20" width="20" class="mx-1 icon"/>
            <span>{{ __('Deposit') }}</span>
        </a>
        <a class="btn btn-border-base d-flex align-items-center justify-content-center w-50"
           href="{{ route('user.wallet.index') }}">
            <x-icon name="wallet-4" height="20" width="20" class="mx-1 icon"/>
            <span>{{ __('Manage') }}</span>
        </a>
    </div>
</div>

@include('frontend.user.wallet.partials._add_wallet_modal')
