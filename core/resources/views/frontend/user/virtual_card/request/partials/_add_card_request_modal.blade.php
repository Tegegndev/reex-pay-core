<div class="modal fade" id="requestVirtualCardModal" tabindex="-1" aria-labelledby="requestVirtualCardModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header sticky-top bg-white" style="z-index:2;">
				<h6 class="modal-title" id="requestVirtualCardModalLabel">{{ __('Request New Virtual Card') }}</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				@if(!$wallets->isEmpty())
					<form action="{{ route('user.virtual-card.request.store') }}" method="post" autocomplete="off">
						@csrf
						<div class="mb-3">
							<label for="cardholder_id" class="form-label fw-semibold">{{ __('Select Cardholder') }}</label>
							<select name="cardholder_id" id="cardholder_id" class="form-select" required aria-required="true">
								<option value="" selected disabled>{{ __('Choose Cardholder') }}</option>
								@foreach($cardholders as $cardholder)
									<option value="{{ $cardholder->id }}">
										@if($cardholder->card_type->isBusiness() && $cardholder->business)
											{{ $cardholder->business->business_name }} ({{ $cardholder->business->contact_email }})
										@else
											{{ $cardholder->full_name }} ({{ $cardholder->email }})
										@endif
									</option>
								@endforeach
							</select>
							<div class="form-text small text-muted">{{ __('Select an approved cardholder profile to request a card.') }}</div>
						</div>
						<div class="mb-3">
							<label for="network" class="form-label fw-semibold">{{ __('Select Network') }}</label>
							<select name="network" id="network" class="form-select" required aria-required="true">
								<option value="" selected disabled>{{ __('Choose a Network') }}</option>
								@foreach(\App\Enums\VirtualCard\VirtualCardNetwork::cases() as $type)
									<option value="{{ $type->value }}">{{ $type->label() }}</option>
								@endforeach
							</select>
							<div class="form-text small text-muted">{{ __('Choose the card network (e.g., Mastercard or Visa) for your new virtual card.') }}</div>
						</div>
						<div class="mb-3">
							<label for="wallet_id" class="form-label fw-semibold">{{ __('Select Wallet') }}</label>
							<div class="input-group">
								<select name="wallet_id" id="wallet_id" class="form-select" required aria-required="true" disabled>
									<option value="" selected disabled>{{ __('Select network first...') }}</option>
								</select>
								<span class="input-group-text bg-transparent border-0" id="wallet-loading" style="display:none;">
                                    <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                                </span>
							</div>
							<div class="form-text small text-muted">{{ __('Select the wallet (currency) you want to link to your new virtual card. Only eligible wallets will be shown after you select a network.') }}</div>
						</div>
						<div class="accordion mb-3" id="cardRequestInfoAccordion">
							<div class="accordion-item border-0">
								<h2 class="accordion-header" id="cardRequestInfoHeading">
									<button class="accordion-button collapsed bg-info-subtle text-dark fw-semibold px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#cardRequestInfoCollapse" aria-expanded="false" aria-controls="cardRequestInfoCollapse">
										<i class="fa-solid fa-circle-info me-2"></i>{{ __('Card Issuing Fee & How to Request') }}
									</button>
								</h2>
								<div id="cardRequestInfoCollapse" class="accordion-collapse collapse" aria-labelledby="cardRequestInfoHeading" data-bs-parent="#cardRequestInfoAccordion">
									<div class="accordion-body bg-info-subtle">
										<div class="mb-2">
											<strong>{{ __('Card Issuing Fee:') }}</strong>
											@if($reqData['min_issue_fee'] == $reqData['max_issue_fee'])
												{{ __('A flat fee of :fee will be charged when your card is approved.', ['fee' => '$' . number_format($reqData['min_issue_fee'], 2)]) }}
											@else
												{{ __('Fee starts from :min to :max depending on provider and card type.', [
													'min' => siteCurrency('symbol') . number_format($reqData['min_issue_fee'],2),
													'max' => siteCurrency('symbol') . number_format($reqData['max_issue_fee'],2)
												]) }}
											@endif
											<br>
											<small class="d-block mt-1">{{ __('The exact fee will be shown after admin approval, based on your selected wallet, network, and provider.') }}</small>
										</div>
										<div class="mb-2">
											<strong>{{ __('How to Request a Card:') }}</strong>
											<ul class="mb-0 ps-4 small">
												<li>{{ __('You must have at least one approved cardholder profile. If you do not, please create and submit a cardholder first.') }}</li>
												<li>{{ __('Admins will review your request and notify you when your card is approved or if more information is needed.') }}</li>
												<li>{{ __('Each card is linked to the selected wallet and cardholder; choose carefully.') }}</li>
												<li>{{ __('Some providers or currencies may require a minimum balance to issue a card.') }}</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex justify-content-between mt-4 gap-2">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
								<x-icon name="x" height="20" width="20"/> {{ __('Close') }}
							</button>
							<button type="submit" class="btn btn-primary">
								<x-icon name="check" height="20" width="20"/> {{ __('Submit Request') }}
							</button>
						</div>
					</form>
				@else
					<div class="alert alert-danger mb-0">
						<p class="mb-0">{{ __("You don't have any wallet to request a virtual card.") }}</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@push('scripts')
	<script>
        "use strict";
        $(function () {
            $('#network').on('change', function () {
                var network = $(this).val();
                $('#wallet_id').prop('disabled', true).html('<option>Loading...</option>');
                $('#wallet-loading').show();
                if (network) {
                    $.get('{{ route("user.virtual-card.request.eligible-wallets") }}', {network: network}, function (wallets) {
                        if (wallets.length) {
                            let html = `<option value="" selected disabled>{{ __('Choose a wallet') }}</option>`;
                            wallets.forEach(function (wallet) {
                                html += `<option value="${wallet.id}">${wallet.text}</option>`;
                            });
                            $('#wallet_id').html(html).prop('disabled', false);
                        } else {
                            $('#wallet_id').html('<option value="">{{ __("No eligible wallets found for this network.") }}</option>').prop('disabled', true);
                        }
                        $('#wallet-loading').hide();
                    }).fail(function () {
                        $('#wallet_id').html('<option value="">{{ __("Error loading wallets.") }}</option>').prop('disabled', true);
                        $('#wallet-loading').hide();
                    });
                } else {
                    $('#wallet_id').html('<option value="" selected disabled>{{ __('Select network first...') }}</option>');
                    $('#wallet-loading').hide();
                }
            });
        });
	</script>
@endpush