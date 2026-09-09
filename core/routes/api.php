<?php

use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StripeController;
use Illuminate\Support\Facades\Route;

Route::middleware('merchant.auth')->group(function () {
    Route::group(['prefix' => 'v1'], function () {
        // Create a payment (stateless; no DB record is stored)
        Route::post('initiate-payment', [PaymentController::class, 'initiatePayment']);

        // Payment Verification
        Route::get('verify-payment/{trxId}', [PaymentController::class, 'verifyPayment']);
    });
});

Route::middleware('auth:sanctum')
    ->post('/stripe/issuing/ephemeral-key', [StripeController::class, 'createEphemeralKey'])->name('stripe.issuing.ephemeral-key');
