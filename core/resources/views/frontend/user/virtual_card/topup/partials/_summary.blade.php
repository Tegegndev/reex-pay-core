<div class="col-xl-5">
	<div class="single-form-card">
		<div class="card-title mb-0">
			<h6 class="text-white mt-2">{{ __('Summary') }}</h6>
		</div>
		<div class="card-main">
			<ul class="summery-list list-unstyled">
				<li class="d-flex justify-content-between">
					<span>{{ __('Source Wallet') }}</span>
					<span><strong>{{ $card->wallet->name ?? '-' }} @lang('Wallet')</strong><span class="ms-2 small">({{ $card->wallet->currency->symbol.number_format($card->wallet->balance ?? 0, 2) }})</span></span>
				</li>
				<li class="d-flex justify-content-between">
					<span>{{ __('Amount to Top Up') }}</span>
					<span class="summary-amount">-</span>
				</li>
				<li class="d-flex justify-content-between">
					<span>{{ __('Top Up Fee') }}</span>
					<span class="summary-charge">
                        @if($cardSettings)
                        	@if($cardSettings->fee_type->value === \App\Enums\FixPctType::PERCENT->value)
                        		{{ $cardSettings->fee_amount }}% ({{ __('Min:') }} {{ $card->wallet->currency->symbol . number_format($cardSettings->min_amount, 2) }})
                        	@else
                        		{{ $card->wallet->currency->symbol . number_format($cardSettings->fee_amount, 2) }}
                        	@endif
                        @else
                        	0
                        @endif
                    </span>
				</li>
				<li class="d-flex justify-content-between">
					<span>{{ __('Total Deducted') }}</span>
					<span class="summary-total text-danger fw-bold">-</span>
				</li>
				<li class="d-flex justify-content-between">
					<span>{{ __('Card Will Receive') }}</span>
					<span class="wallet-added text-success fw-bold">-</span>
				</li>
				<li class="d-flex justify-content-between">
					<span>{{ __('Currency') }}</span>
					<span>{{ $card->wallet->currency->code ?? '' }}</span>
				</li>
			</ul>
			<div class="mt-2">
				@if($cardSettings)
					<span class="badge bg-info text-dark">{{ __('Approval Required Above:') }} {{ $card->wallet->currency->symbol . number_format($cardSettings->approval_threshold, 2) }}</span>
				@else
					<span class="badge bg-success">{{ __('Instant Top-up. No approval needed.') }}</span>
				@endif
			</div>
		</div>
	</div>
</div>
