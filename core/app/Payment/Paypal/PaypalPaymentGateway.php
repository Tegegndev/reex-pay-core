<?php

namespace App\Payment\Paypal;

use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use Transaction;

class PaypalPaymentGateway implements PaymentGatewayInterface
{
    private PayPalClient $client;

    public function __construct()
    {

        $this->client = new PayPalClient;
    }

    public function deposit($amount, $currency, $trxId)
    {
        $paymentPayload = [
            'intent' => 'sale',
            'payer'  => [
                'payment_method' => 'paypal',
            ],
            'transactions' => [
                [
                    'amount' => [
                        'total'    => number_format($amount, 2),
                        'currency' => $currency,
                    ],
                    'description' => $trxId,
                ],
            ],
            'redirect_urls' => [
                'return_url' => route('ipn.handle', ['gateway' => 'paypal']),
                'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
            ],
        ];

        $payment = $this->client->createPayment($paymentPayload);
        Session::put('cancel_tnx', $trxId);

        foreach ($payment->links as $link) {
            if ($link->rel === 'approval_url') {
                return $link->href;
            }
        }

        return true;
    }

    public function withdraw($amount, $currency, $trxId, $withdrawCredential)
    {
        // Build PayPal Payouts request payload
        $payoutData = [
            'sender_batch_header' => [
                'sender_batch_id' => uniqid(), // Unique ID for tracking
                'email_subject'   => 'You have a payout!',
                'email_message'   => 'You have received a payout!',
            ],
            'items' => [
                [
                    'recipient_type' => 'EMAIL',
                    'receiver'       => $withdrawCredential,
                    'note'           => 'Withdrawal Payout',
                    'sender_item_id' => $trxId,
                    'amount'         => [
                        'value'    => number_format($amount, 2, '.', ''),
                        'currency' => $currency,
                    ],
                ],
            ],
        ];

        $this->client->payoutPayment($payoutData);
    }

    public function handleIPN(Request $request)
    {
        // If 'event_type' is present, this is a PayPal webhook event.
        if ($request->has('event_type')) {
            return $this->handleWebhookEvent($request);
        }

        // Otherwise, it's the user returning from PayPal (execute the payment).
        return $this->handleExecutePayment($request);

    }

    protected function handleWebhookEvent(Request $request): JsonResponse
    {
        // Extract the event type and log the full payload for debugging purposes
        $eventType = $request->input('event_type');
        Log::info("Received PayPal Webhook: {$eventType}", $request->all());

        // Extract the sender item ID (your transaction ID used in payout creation)
        $payoutItemId = $request->input('resource.payout_item.sender_item_id', null);

        // Handle different event types
        switch ($eventType) {
            case 'PAYMENT.PAYOUTS-ITEM.SUCCESS':
                // Mark the transaction as successful
                Transaction::completeTransaction($payoutItemId);
                break;

            case 'PAYMENT.PAYOUTSBATCH.PROCESSING':
                // Ignore processing events (no action needed)
                Log::info('Payout batch is still processing. No action taken.');
                break;

            default:
                // Handle all other scenarios with a unified failure action
                if ($payoutItemId) {
                    Transaction::cancelTransaction(
                        $payoutItemId,
                        __('Your withdrawal request was unsuccessful. The amount has been refunded. Please contact the administrator for assistance.'),
                        true
                    );
                }
                Log::warning("Unhandled PayPal Webhook event type: {$eventType}");
                break;
        }

        // Return a success response to PayPal to acknowledge receipt of the webhook
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle the payment execution flow when the user returns from PayPal.
     */
    protected function handleExecutePayment(Request $request): RedirectResponse
    {
        // Validate that required parameters exist; you can add further validation as needed.
        $request->validate([
            'paymentId' => 'required|string',
            'PayerID'   => 'required|string',
        ]);

        // Execute the payment via your PayPal client
        $response = $this->client->executePayment(
            $request->input('paymentId'),
            $request->input('PayerID')
        );

        // Check the response state
        if (isset($response->state) && $response->state === 'approved') {
            // Fetch your transaction ID (assuming you stored it in the 'description')
            $trxId = $response->transactions[0]->description ?? null;

            // Mark the transaction as complete in your system
            Transaction::completeTransaction($trxId);

            // Notify and redirect
            notifyEvs('success', __('Payment Successful'));

            return redirect()->route('status.success', ['trx_id' => $trxId]);
        }

        // If not approved, handle failure accordingly
        notifyEvs('warning', __('Something went wrong. Please try again.'));

        return redirect()->back();
    }
}
