<?php

namespace App\Payment\Instamojo;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class InstamojoPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('instamojo');
        $this->baseUrl = $this->credentials['sandbox'] 
            ? 'https://test.instamojo.com/api/1.1/' 
            : 'https://www.instamojo.com/api/1.1/';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create payment request
        $paymentData = [
            'purpose' => 'Payment for ' . setting('site_title'),
            'amount' => number_format($amount, 2, '.', ''),
            'phone' => $this->credentials['phone'] ?? '9999999999',
            'buyer_name' => auth()->user()->name ?? 'Customer',
            'redirect_url' => route('ipn.handle', ['gateway' => 'instamojo']),
            'send_email' => 'False',
            'webhook' => route('ipn.handle', ['gateway' => 'instamojo']),
            'send_sms' => 'False',
            'email' => auth()->user()->email ?? 'customer@example.com',
            'allow_repeated_payments' => 'False'
        ];

        $response = Http::withHeaders([
            'X-Api-Key' => $this->credentials['api_key'],
            'X-Auth-Token' => $this->credentials['auth_token'],
        ])->asForm()->post($this->baseUrl . 'payment-requests/', $paymentData);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['success']) {
                // Store payment request ID for verification
                cache()->put('instamojo_request_' . $trxId, $result['payment_request']['id'], 3600);
                
                return $result['payment_request']['longurl'];
            }
        }

        throw new \Exception('Failed to create Instamojo payment request: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Instamojo IPN received', $request->all());

        try {
            $paymentId = $request->input('payment_id');
            $paymentRequestId = $request->input('payment_request_id');
            $paymentStatus = $request->input('payment_status');

            if (!$paymentId || !$paymentRequestId) {
                Log::error('Instamojo IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Verify payment with Instamojo API
            $verificationResponse = Http::withHeaders([
                'X-Api-Key' => $this->credentials['api_key'],
                'X-Auth-Token' => $this->credentials['auth_token'],
            ])->get($this->baseUrl . 'payment-requests/' . $paymentRequestId . '/' . $paymentId . '/');

            if ($verificationResponse->successful()) {
                $verification = $verificationResponse->json();
                
                if ($verification['success'] && $verification['payment_request']['payment']['status'] === 'Credit') {
                    // Find transaction ID from cache or payment purpose
                    $transactionId = $this->findTransactionId($paymentRequestId);
                    
                    if ($transactionId) {
                        Transaction::completeTransaction($transactionId);
                        Log::info('Instamojo payment completed for transaction: ' . $transactionId);
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Instamojo IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function findTransactionId($paymentRequestId): ?string
    {
        // Try to find transaction ID from cache
        $cacheKeys = cache()->getMemcachedKeys();
        
        foreach ($cacheKeys as $key) {
            if (strpos($key, 'instamojo_request_') === 0) {
                $cachedRequestId = cache()->get($key);
                if ($cachedRequestId === $paymentRequestId) {
                    return str_replace('instamojo_request_', '', $key);
                }
            }
        }

        return null;
    }
}
