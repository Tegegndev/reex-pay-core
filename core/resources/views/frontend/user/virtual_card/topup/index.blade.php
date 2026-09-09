@extends('frontend.layouts.user.index')
@section('title', __('Virtual Card Top Up'))
@section('content')
	<div class="row">
		<div class="col-xl-7 mx-auto">
			<div class="single-form-card">
				<div class="card-title mb-0 d-flex flex-wrap justify-content-between align-items-center">
					<h6 class="mb-0 text-white">{{ __('Top Up Virtual Card') }}</h6>
					<a class="btn btn-light-success btn-sm" href="{{ route('user.transaction.index', ['type' => \App\Enums\TrxType::CARD_TOPUP]) }}">
						<i class="fas fa-list"></i> {{ __('History') }}
					</a>
				</div>
				<div class="card-main">
					{{-- Card Info --}}
					<div class="mb-4">
						<div class="credit-card-mockup position-relative rounded-4 shadow-sm p-3 text-white visa-pro-card">
							<div class="d-flex justify-content-between align-items-center mb-2">
								<span class="fw-bold fs-5">{{ $card->card_name ?? 'Virtual Card' }}</span>
								<span>
                                <x-icon name="{{ strtolower($card->brand) }}" height="36"/>
                            </span>
							</div>
							<div class="fs-5 text-monospace mb-2">
								•••• •••• •••• <b>{{ $card->last4 }}</b>
							</div>
							<div class="d-flex justify-content-between small">
								<span>{{ __('Expiry') }}: {{ $card->expiry_month }}/{{ $card->expiry_year }}</span>
								<span>{{ $card->wallet->currency->code ?? '' }}</span>
							</div>
						</div>
					</div>
					{{-- Top Up Form --}}
					<div class="alert alert-info d-flex align-items-center mb-3" role="alert">
						<i class="fa-solid fa-wallet me-2"></i>
						<span>
                        {{ __('Funds will be deducted from:') }}
                        <strong>{{ $card->wallet->name ?? '-' }} @lang('Wallet')</strong>
                        <span class="ms-2 small">({{ __('Available:') }} {{ $card->wallet->currency->symbol.number_format($card->wallet->balance ?? 0, 2) }})</span>
                    </span>
					</div>
					<form action="{{ route('user.virtual-card.topup-store') }}" method="post" onsubmit="disableSubmitButton(this, '{{ __('Processing...') }}')">
						@csrf
						<input type="hidden" name="card_id" value="{{ $card->id ?? '' }}">
						<div class="single-input-inner style-border mb-3">
							<label class="form-label">{{ __('Amount') }}</label>
							<div class="input-group">
								<input type="text" step="0.01"
								       min="{{ $cardSettings->min_amount ?? '0.01' }}"
								       @if(!empty($cardSettings->max_amount)) max="{{ $cardSettings->max_amount }}" @endif
								       class="form-control" name="amount"
								       oninput="this.value = validateDouble(this.value)"
								       placeholder="{{ __('Enter Amount') }}"
								       required>
								<span class="input-group-text">{{ $card->wallet->currency->code ?? siteCurrency() }}</span>
							</div>
							<small class="text-muted">
								@if($cardSettings)
									{{ __('Min:') }} {{ $card->wallet->currency->symbol . number_format($cardSettings->min_amount, 2) }}
									| {{ __('Max:') }} {{ $card->wallet->currency->symbol . number_format($cardSettings->max_amount, 2) }}
								@else
									{{ __('No top-up limits. Any amount allowed.') }}
								@endif
							</small>
						</div>
						<button type="submit" class="btn btn-base w-100 submit-btn">
							<x-icon name="check" height="20"/>
							{{ __('Top Up Now') }}
						</button>
					</form>
				</div>
			</div>
		</div>

		@include('frontend.user.virtual_card.topup.partials._summary')

	</div>
@endsection

@push('scripts')
	<script>
        $(document).ready(function () {
            function calculateSummary() {
                var amount = parseFloat($("input[name='amount']").val()) || 0;
                var feeType = @json($cardSettings?->fee_type?->value ?? null);
                var feeAmount = parseFloat(@json($cardSettings?->fee_amount ?? 0));
                var currencySymbol = @json($card->wallet->currency->symbol ?? '$');
                var fee = 0;
                var total = amount;
                var cardReceive = amount;

                if (amount > 0) {
                    if (feeType === 'percent') {
                        fee = (amount * feeAmount / 100);
                    } else if (feeType === 'fixed') {
                        fee = feeAmount;
                    }
                    total = amount + fee;
                    cardReceive = amount;
                }
                $(".summary-amount").text(amount > 0 ? currencySymbol + amount.toFixed(2) : '-');
                $(".summary-charge").text(amount > 0 ? currencySymbol + fee.toFixed(2) : '-');
                $(".summary-total").text(amount > 0 ? '-'+currencySymbol + total.toFixed(2) : '-');
                $(".wallet-added").text(amount > 0 ? '+'+currencySymbol + cardReceive.toFixed(2) : '-');
            }
            $("input[name='amount']").on('input', calculateSummary);
            calculateSummary();
        });
	</script>
@endpush