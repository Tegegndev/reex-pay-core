"use strict";

async function handleStripeCardDetails(cardId, modalBody,stripeKey) {
    try {
        const stripe = Stripe(stripeKey);
        const nonceResult = await stripe.createEphemeralKeyNonce({issuingCard: cardId});
        const nonce = nonceResult.nonce;
        const res = await fetch("/api/stripe/ephemeral-key", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({card_id: cardId, nonce})
        });
        const data = await res.json();
        if (data.error || !data.ephemeralKeySecret || !data.stripeCardId || !data.publishableKey) {
            modalBody.innerHTML = '<div class="alert alert-danger">Unable to retrieve card details.</div>';
            return;
        }
        mountStripeIssuingElements({
            stripeCardId: data.stripeCardId,
            ephemeralKeySecret: data.ephemeralKeySecret,
            nonce: nonce,
            mountSelector: '#card-details-content'
        });
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load card details.</div>';
    }
}

function mountStripeIssuingElements({stripeCardId, ephemeralKeySecret, nonce, mountSelector}) {
    const mountNode = document.querySelector(mountSelector);
    if (!mountNode) return;
    mountNode.innerHTML = '';
    try {
        const elements = stripe.elements();
        const cardNumber = elements.create('issuingCardNumberDisplay', {
            issuingCard: stripeCardId,
            nonce: nonce,
            ephemeralKeySecret: ephemeralKeySecret,
            style: {base: {fontSize: '16px', color: '#32325d'}}
        });
        const cardExpiry = elements.create('issuingCardExpiryDisplay', {
            issuingCard: stripeCardId,
            nonce: nonce,
            ephemeralKeySecret: ephemeralKeySecret,
            style: {base: {fontSize: '16px', color: '#32325d'}}
        });
        const cardCvc = elements.create('issuingCardCvcDisplay', {
            issuingCard: stripeCardId,
            nonce: nonce,
            ephemeralKeySecret: ephemeralKeySecret,
            style: {base: {fontSize: '16px', color: '#32325d'}}
        });
        mountNode.innerHTML = `
<div class="credit-card-mockup bg-gradient bg-primary text-white rounded-5 shadow-lg p-4 mb-3 position-relative"
     style="max-width:390px;margin:auto;border-radius:32px;box-shadow:0 6px 32px #0003;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-bold fs-4">
            <i class='fa-solid fa-credit-card'></i>
        </span>
        <span>
            <img src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/svgs/brands/cc-visa.svg"
                 alt="Visa" style="height:34px;">
        </span>
    </div>
    <div class="mb-4">
        <label class="form-label text-white-50 small mb-1">Card Number</label>
        <div id="card-number" class="fs-5 fw-bold" style="letter-spacing:0.13em;min-height:32px;"></div>
    </div>
    <div class="row g-0">
        <div class="col-6 pe-2">
            <label class="form-label text-white-50 small mb-1 d-block">Expiry</label>
            <div id="card-expiry" class="fs-6 fw-semibold"></div>
        </div>
        <div class="col-6 ps-2">
            <div class="d-flex flex-column align-items-end">
                <label class="form-label text-white-50 small mb-1">CVC</label>
                <div id="card-cvc" class="fs-6 fw-semibold  text-center" style="min-width:30px;"></div>
            </div>
        </div>
    </div>
</div>
`;
        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');
    } catch (e) {
        mountNode.innerHTML = '<div class="alert alert-danger">Stripe Elements error: ' + e.message + '</div>';
    }
}