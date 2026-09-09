"use strict";

function handleStroWalletCardDetails(cardId,providerName, modalBody) {
    modalBody.html('<div class="alert alert-info">StroWallet card details loading...</div>');

    $.ajax({
        url: 'card-details/' + cardId + '/' + providerName,
        method: 'GET',
        success: function(response) {
            modalBody.html(response);
        },
        error: function() {
            modalBody.html('<div class="alert alert-danger">Failed to load card details. Please try again.</div>');
        }
    });
}