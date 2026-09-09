<?php

namespace App\Payment\Airtel;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Transaction;

class AirtelPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('airtel');
        $this->baseUrl = $this->credentials['sandbox'] 
            ? 'https://openapiuat.airtel.africa' 
            : 'https://openapi.airtel.africa';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Get access token first
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            throw new \Exception('Failed to get Airtel access token');
        }

        // Initiate payment collection
        $collectionData = [
            'reference' => $trxId,
            'subscriber' => [
                'country' => strtoupper($this->credentials['country']),
                'currency' => strtoupper($currency),
                'msisdn' => $this->credentials['test_msisdn'] ?? '256704000000'
            ],
            'transaction' => [
                'amount' => (float) $amount,
                'country' => strtoupper($this->credentials['country']),
                'currency' => strtoupper($currency),
                'id' => $trxId
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'X-Country' => strtoupper($this->credentials['country']),
            'X-Currency' => strtoupper($currency),
        ])->post($this->baseUrl . '/merchant/v1/payments/', $collectionData);

        if ($response->successful()) {
            $result = $response->json();
            
            if (isset($result['status']) && $result['status'] === 'TXN_201') {
                // Store transaction for monitoring
                cache()->put('airtel_transaction_' . $trxId, $result['data']['transaction']['id'], 3600);
                
                // Return custom payment page for user confirmation
                return route('payment.airtel.checkout', [
                    'trx_id' => $trxId,
                    'airtel_txn_id' => $result['data']['transaction']['id']
                ]);
            }
        }

        throw new \Exception('Failed to initiate Airtel payment: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Airtel Money IPN received', $request->all());

        try {
            $transactionId = $request->input('transaction_id');
            $airtelTxnId = $request->input('airtel_transaction_id');
            $status = $request->input('status');

            if (!$transactionId) {
                Log::error('Airtel IPN missing transaction_id');
                return response()->json(['error' => 'Missing transaction ID'], 400);
            }

            // Get access token for verification
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                Log::error('Failed to get Airtel access token for verification');
                return response()->json(['error' => 'Token error'], 500);
            }

            // Check transaction status with Airtel API
            $airtelTxnId = cache()->get('airtel_transaction_' . $transactionId);
            
            if ($airtelTxnId) {
                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'X-Country' => strtoupper($this->credentials['country']),
                    'X-Currency' => strtoupper($this->credentials['currency']),
                ])->get($this->baseUrl . '/standard/v1/payments/' . $airtelTxnId);

                if ($statusResponse->successful()) {
                    $statusData = $statusResponse->json();
                    
                    if (isset($statusData['status']) && $statusData['status'] === 'TXN_200') {
                        Transaction::completeTransaction($transactionId);
                        Log::info('Airtel payment completed for transaction: ' . $transactionId);
                        
                        // Clear cache
                        cache()->forget('airtel_transaction_' . $transactionId);
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Airtel IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function getAccessToken(): ?string
    {
        // Check cache first
        $cacheKey = 'airtel_access_token_' . $this->credentials['client_id'];
        $token = cache()->get($cacheKey);
        
        if ($token) {
            return $token;
        }

        // Get new token
        $authData = [
            'client_id' => $this->credentials['client_id'],
            'client_secret' => $this->credentials['client_secret'],
            'grant_type' => 'client_credentials'
        ];

        $response = Http::asForm()->post($this->baseUrl . '/auth/oauth2/token', $authData);

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
