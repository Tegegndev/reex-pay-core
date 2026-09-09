<?php

namespace App\Payment\Coinbase;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class CoinbasePaymentGateway implements PaymentGatewayInterface
{
    protected string $apiBaseUrl;

    protected mixed $apiKey;

    protected mixed $webhookSecret;

    /**
     * @throws Exception
     */
    public function __construct()
    {

        // Load Coinbase credentials from the database or environment
        $credentials         = PaymentGateway::getCredentials('coinbase');
        $this->apiBaseUrl    = 'https://api.commerce.coinbase.com'; // Coinbase API Base URL
        $this->apiKey        = $credentials['api_key'];
        $this->webhookSecret = $credentials['webhook_secret'];
    }

    /**
     * Create a new deposit (payment) via Coinbase.
     *
     * @param  float  $amount
     * @param  string $currency
     * @param  string $trxId
     * @return mixed
     */
    public function deposit($amount, $currency, $trxId)
    {
        try {
            $chargeData = [
                'name'        => 'Deposit Transaction',
                'description' => 'Transaction ID: '.$trxId,
                'local_price' => [
                    'amount'   => $amount,
                    'currency' => $currency,
                ],
                'pricing_type' => 'fixed_price',
                'metadata'     => [
                    'trx_id' => $trxId,
                ],
                'redirect_url' => route('status.success', ['trx_id' => $trxId]),
                'cancel_url'   => route('status.cancel', ['trx_id' => $trxId]),
            ];

            $response = Http::withHeaders([
                'X-CC-Api-Key' => $this->apiKey,
                'X-CC-Version' => '2018-03-22',
            ])->post($this->apiBaseUrl.'/charges', $chargeData);

            if ($response->successful()) {
                $charge = $response->json();

                return $charge['data']['hosted_url']; // Return Coinbase payment URL
            } else {
                Log::error('Coinbase API error: '.$response->body());

                return false;
            }
        } catch (Exception $e) {
            Log::error('Failed to create Coinbase deposit: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Handle Coinbase IPN (Instant Payment Notification).
     *
     * @return void
     */
    public function handleIPN(Request $request)
    {
        if (! $this->validateWebhookSignature($request)) {
            Log::error('Invalid Coinbase Webhook Signature.');

            return;
        }

        $trxId = $request->input('event.data.metadata.trx_id');

        if ($trxId) {
            try {
                Transaction::completeTransaction($trxId);
                Log::info("Transaction $trxId completed successfully.");
            } catch (Exception $e) {
                Log::error("Failed to complete transaction $trxId: ".$e->getMessage());
            }
        } else {
            Log::warning('Invalid IPN received, missing trx_id.');
        }
    }

    /**
     * Validate Webhook Signature.
     *
     * @return bool
     */
    private function validateWebhookSignature(Request $request)
    {
        $signatureHeader   = $request->header('X-CC-Webhook-Signature');
        $payload           = $request->getContent();
        $computedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);

        if (hash_equals($computedSignature, $signatureHeader)) {
            return true;
        }

        Log::warning('Coinbase Webhook Signature validation failed.');

        return false;
    }
}
