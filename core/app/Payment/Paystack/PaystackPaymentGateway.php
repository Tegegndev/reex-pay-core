<?php

namespace App\Payment\Paystack;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Transaction;

class PaystackPaymentGateway implements PaymentGatewayInterface
{
    private const PAYSTACK_API_URL = 'https://api.paystack.co/transaction';

    protected Client $client;

    private string $publicKey;

    private string $secretKey;

    private string $merchantEmail;

    public function __construct()
    {
        // Get credentials from PaymentGateway model
        $credentials = PaymentGateway::getCredentials('paystack');

        // Check if keys are set
        if (empty($credentials['secret_key']) || empty($credentials['public_key']) || empty($credentials['merchant_email'])) {
            throw new Exception('Paystack API credentials are not set.');
        }

        // Assign credentials to properties
        $this->publicKey     = $credentials['public_key'];
        $this->secretKey     = $credentials['secret_key'];
        $this->merchantEmail = $credentials['merchant_email'];

        // Initialize Guzzle client with authorization header
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bearer '.$this->secretKey,
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Prepare payment payload
        $paymentPayload = [
            'email'        => $this->merchantEmail, // Use merchant email or user's email
            'amount'       => $amount * 100, // Convert to kobo
            'currency'     => $currency,
            'callback_url' => route('status.success'),
            'metadata'     => [
                'order_id' => $trxId,
            ],
        ];

        try {
            // Send request to Paystack's initialize endpoint
            $response = $this->client->post(self::PAYSTACK_API_URL.'/initialize', [
                'json' => $paymentPayload,
            ]);

            $payment = json_decode($response->getBody(), true);

            // Check if authorization URL is returned
            if (! empty($payment['data']['authorization_url'])) {
                return $payment['data']['authorization_url'];
            }

            Log::error('Paystack: Invalid payment response', $payment);

            return back()->withErrors(['error' => 'Failed to initiate payment, please try again.']);

        } catch (GuzzleException $e) {
            $response  = $e->getResponse();
            $errorBody = $response ? $response->getBody()->getContents() : null;
            Log::error('Paystack Payment Error: '.$e->getMessage(), ['response' => $errorBody]);

            return back()->withErrors(['error' => 'Payment initiation failed. Please contact support.']);
        }
    }

    public function handleIPN(Request $request): JsonResponse
    {
        // Extract reference from JSON payload
        $reference = $request->input('data.reference');

        if (! $reference) {
            Log::error('Paystack Webhook Error: Missing reference in payload');

            return response()->json(['error' => 'Missing reference'], 400);
        }

        try {
            // Send request to Paystack's verify endpoint
            $response = $this->client->get(self::PAYSTACK_API_URL.'/verify/'.$reference);
            $payment  = json_decode($response->getBody(), true);

            if ($payment['data']['status'] === 'success') {
                Transaction::completeTransaction($payment['data']['metadata']['order_id']);

                return response()->json(['status' => 'success']);
            } else {
                Transaction::failTransaction($payment['data']['metadata']['order_id']);

                return response()->json(['status' => 'failed']);
            }

        } catch (GuzzleException $e) {
            Log::error('Paystack Webhook Error: '.$e->getMessage());

            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }
}
