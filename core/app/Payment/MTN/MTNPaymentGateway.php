<?php

namespace App\Payment\MTN;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Transaction;

class MTNPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('mtn');
        $this->baseUrl = $this->credentials['sandbox'] 
            ? 'https://sandbox.momodeveloper.mtn.com' 
            : 'https://momodeveloper.mtn.com';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Get access token first
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            throw new \Exception('Failed to get MTN access token');
        }

        // Create payment request
        $requestToPay = [
            'amount' => (string) $amount,
            'currency' => strtoupper($currency),
            'externalId' => $trxId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $this->credentials['test_msisdn'] ?? '256774290781'
            ],
            'payerMessage' => 'Payment for ' . setting('site_title'),
            'payeeNote' => 'Payment received from ' . setting('site_title')
        ];

        $referenceId = Str::uuid()->toString();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => $this->credentials['sandbox'] ? 'sandbox' : 'live',
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key']
        ])->post($this->baseUrl . '/collection/v1_0/requesttopay', $requestToPay);

        if ($response->successful() || $response->status() === 202) {
            // Store reference ID for later verification
            cache()->put('mtn_reference_' . $trxId, $referenceId, 3600);
            
            // Return a custom payment page URL (you'll need to create this)
            return route('payment.mtn.checkout', ['trx_id' => $trxId, 'ref' => $referenceId]);
        }

        throw new \Exception('Failed to create MTN payment request: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('MTN IPN received', $request->all());

        try {
            $transactionId = $request->input('transaction_id');
            $referenceId = $request->input('reference_id');
            $status = $request->input('status');

            if (!$transactionId || !$referenceId) {
                Log::error('MTN IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Verify transaction status with MTN API
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                Log::error('Failed to get MTN access token for verification');
                return response()->json(['error' => 'Token error'], 500);
            }

            $verificationResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'X-Target-Environment' => $this->credentials['sandbox'] ? 'sandbox' : 'live',
                'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key']
            ])->get($this->baseUrl . '/collection/v1_0/requesttopay/' . $referenceId);

            if ($verificationResponse->successful()) {
                $payment = $verificationResponse->json();
                
                if ($payment['status'] === 'SUCCESSFUL') {
                    Transaction::completeTransaction($transactionId);
                    Log::info('MTN payment completed for transaction: ' . $transactionId);
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('MTN IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function getAccessToken(): ?string
    {
        // Check cache first
        $cacheKey = 'mtn_access_token';
        $token = cache()->get($cacheKey);
        
        if ($token) {
            return $token;
        }

        // Get new token
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->credentials['subscription_key'],
            'Authorization' => 'Basic ' . base64_encode($this->credentials['user_id'] . ':' . $this->credentials['api_key'])
        ])->post($this->baseUrl . '/collection/token/');

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['access_token'];
            $expiresIn = $data['expires_in'] ?? 3600;
            
            // Cache token for slightly less than expiry time
            cache()->put($cacheKey, $token, $expiresIn - 300);
            
            return $token;
        }

        return null;
    }
}
