@php
	use App\Models\PaymentGateway;
	$stripeCredentials = PaymentGateway::getCredentials('stripe');
@endphp
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('frontend/js/virtualCardProvider/stripe.js') }}"></script>
<script src="{{ asset('frontend/js/virtualCardProvider/strowallet.js') }}"></script>
<script>
    "use strict";
    $(document).ready(function () {
        $('.show-card-details').on('click', function () {
            const btn = $(this);
            const cardId = btn.data('cardId');
            const provider = btn.data('provider');
            const modalBody = $('#card-details-content');

            if (!cardId || !modalBody.length) return;

            modalBody.html('<div class="text-center py-5"><span class="spinner-border text-primary"></span></div>');

            if (provider === 'stripe') {
                const stripeKey = "{{ $stripeCredentials['stripe_key'] }}";
                handleStripeCardDetails(cardId, modalBody,stripeKey);
            } else if (provider === 'strowallet') {
                handleStroWalletCardDetails(cardId, provider, modalBody);
            }
        });


        $('.demo-toggle-details').on('click', function () {
            var cardId = $(this).data('cardid');
            var masked = $('#masked-' + cardId);
            var full = $('#full-demo-' + cardId);
            var cvcMasked = $('#cvc-masked-' + cardId);
            var cvcFull = $('#cvc-full-demo-' + cardId);
            var eyeIcon = $('#eye-icon-' + cardId);

            masked.toggleClass('d-none');
            full.toggleClass('d-none');
            if (cvcMasked.length && cvcFull.length) {
                cvcMasked.toggleClass('d-none');
                cvcFull.toggleClass('d-none');
            }
            eyeIcon.toggleClass('fa-eye fa-eye-slash');
        });
    });
</script>