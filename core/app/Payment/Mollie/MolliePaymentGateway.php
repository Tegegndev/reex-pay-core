<?php

namespace App\Payment\Mollie;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Transaction;

class MolliePaymentGateway implements PaymentGatewayInterface
{
    protected MollieApiClient $mollie;

    public function __construct()
    {
        $credentials = PaymentGateway::getCredentials('mollie');

        if (empty($credentials['api_key'])) {
            throw new Exception('Mollie API key is not configured.');
        }

        $this->mollie = new MollieApiClient;
        $this->mollie->setApiKey(trim($credentials['api_key']));
    }

    public function deposit($amount, $currency, $trxId)
    {
        try {
            $payment = $this->mollie->payments->create([
                'amount' => [
                    'currency' => $currency,
                    'value'    => number_format($amount, 2, '.', ''), // "10.00"
                ],
                'description' => setting('site_title') ?? 'Wallet Deposit',
                'redirectUrl' => route('status.success', ['trx_id' => $trxId]),
                'webhookUrl'  => route('ipn.handle', ['gateway' => 'mollie']),
                'metadata'    => [
                    'order_id' => $trxId,
                ],
            ]);

            return $payment->getCheckoutUrl(); // Mollie hosted payment page

        } catch (ApiException $e) {

            Log::error('Mollie API error: '.$e->getMessage(), [
                'code'  => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Mollie payment failed to start.']);
        }
    }

    public function handleIPN(Request $request): JsonResponse
    {
        $paymentId = $request->input('id');

        if (! $paymentId) {
            Log::warning('Mollie Webhook: Missing payment ID.');

            return response()->json(['error' => 'Invalid webhook payload.'], 400);
        }

        try {
            $payment = $this->mollie->payments->get($paymentId);
            $orderId = $payment->metadata->order_id ?? null;

            if ($payment->isPaid()) {
                Transaction::completeTransaction($orderId);

                return response()->json(['status' => 'success']);
            }

            Transaction::failTransaction($orderId);

            return response()->json(['status' => $payment->status]);

        } catch (ApiException $e) {
            Log::error('Mollie Webhook Exception: '.$e->getMessage());

            return response()->json(['error' => 'Mollie webhook failed.'], 400);
        }
    }
}
