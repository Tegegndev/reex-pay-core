<?php

namespace App\Payment\Flutterwave;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Transaction;

class FlutterwavePaymentGateway implements PaymentGatewayInterface
{
    private const FLUTTERWAVE_API_URL = 'https://api.flutterwave.com/v3';

    protected Client $client;

    private string $publicKey;

    private string $secretKey;

    public function __construct()
    {
        $credentials = PaymentGateway::getCredentials('flutterwave');

        if (empty($credentials['secret_key']) || empty($credentials['public_key'])) {
            throw new Exception('Flutterwave API credentials are not set.');
        }

        $this->publicKey = $credentials['public_key'];
        $this->secretKey = $credentials['secret_key'];

        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->secretKey,
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function deposit($amount, $currency, $trxId)
    {
        $paymentPayload = [
            'tx_ref'       => $trxId,
            'amount'       => $amount,
            'currency'     => $currency,
            'redirect_url' => route('status.success'),
            'customer'     => [
                'email' => auth()->user()->email ?? null, // Replace with actual customer email
            ],
            'customizations' => [
                'title'       => setting('site_title'),
                'description' => 'Payment for order '.$trxId,
            ],
        ];

        try {
            $response = $this->client->post(self::FLUTTERWAVE_API_URL.'/payments', [
                'json' => $paymentPayload,
            ]);

            $payment = json_decode($response->getBody(), true);

            if (! empty($payment['data']['link'])) {
                return $payment['data']['link'];
            }

            Log::error('Flutterwave: Invalid payment response', $payment);

            return back()->withErrors(['error' => 'Failed to initiate payment, please try again.']);

        } catch (GuzzleException $e) {
            $response  = $e->getResponse();
            $errorBody = $response ? $response->getBody()->getContents() : null;
            Log::error('Flutterwave Payment Error: '.$e->getMessage(), ['response' => $errorBody]);

            return back()->withErrors(['error' => 'Payment initiation failed. Please contact support.']);
        }
    }

    public function handleIPN(Request $request): JsonResponse
    {
        // Validate event type
        $event = $request->input('event');
        if ($event !== 'charge.completed') {
            Log::warning('Unhandled webhook event: '.$event);

            return response()->json(['message' => 'Event not handled'], 200);
        }

        // Retrieve payload data
        $data = $request->input('data');
        if (! $data || ! isset($data['id'], $data['tx_ref'])) {
            return response()->json(['error' => 'Invalid webhook payload'], 400);
        }

        $transactionId = $data['id'];
        $txRef         = $data['tx_ref'];

        // Optional delay for consistency
        sleep(2);

        try {
            // Call Flutterwave API to verify transaction
            $response = $this->client->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify", [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->secretKey,
                    'Content-Type'  => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            // Validate response structure
            if (
                isset($result['status'], $result['data']) && $result['status'] === 'success' && $result['data']['status'] === 'successful'
            ) {
                // Process successful transaction
                Transaction::completeTransaction($txRef);

                return response()->json(['status' => 'success']);
            }

            // If status not successful
            Transaction::failTransaction($txRef);

            return response()->json(['status' => 'failed']);

        } catch (GuzzleException $e) {
            Log::error('Flutterwave Webhook Verification Error', [
                'error'          => $e->getMessage(),
                'transaction_id' => $transactionId,
                'tx_ref'         => $txRef,
            ]);

            return response()->json(['error' => 'Verification failed'], 400);
        }
    }
}
