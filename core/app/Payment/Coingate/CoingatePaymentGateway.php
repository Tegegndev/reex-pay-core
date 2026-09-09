<?php

namespace App\Payment\Coingate;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class CoingatePaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('coingate');
        $this->baseUrl = $this->credentials['sandbox'] 
            ? 'https://api-sandbox.coingate.com' 
            : 'https://api.coingate.com';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create CoinGate order
        $orderData = [
            'order_id' => $trxId,
            'price_amount' => (float) $amount,
            'price_currency' => strtoupper($currency),
            'receive_currency' => strtoupper($this->credentials['receive_currency'] ?? 'EUR'),
            'callback_url' => route('ipn.handle', ['gateway' => 'coingate']),
            'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
            'success_url' => route('status.success', ['trx_id' => $trxId]),
            'title' => 'Payment for ' . setting('site_title'),
            'description' => 'Payment transaction #' . $trxId,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->credentials['auth_token'],
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/v2/orders', $orderData);

        if ($response->successful()) {
            $order = $response->json();
            return $order['payment_url'];
        }

        throw new \Exception('Failed to create CoinGate order: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('CoinGate IPN received', $request->all());

        try {
            $orderId = $request->input('order_id');
            $status = $request->input('status');
            $token = $request->input('token');

            if (!$orderId) {
                Log::error('CoinGate IPN missing order_id');
                return response()->json(['error' => 'Missing order ID'], 400);
            }

            // Verify the callback with CoinGate API
            $verificationResponse = Http::withHeaders([
                'Authorization' => 'Token ' . $this->credentials['auth_token'],
            ])->get($this->baseUrl . '/v2/orders/' . $orderId);

            if ($verificationResponse->successful()) {
                $order = $verificationResponse->json();
                
                if ($order['status'] === 'paid') {
                    Transaction::completeTransaction($orderId);
                    Log::info('CoinGate payment completed for transaction: ' . $orderId);
                } else {
                    Log::info('CoinGate payment status: ' . $order['status'] . ' for transaction: ' . $orderId);
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('CoinGate IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
