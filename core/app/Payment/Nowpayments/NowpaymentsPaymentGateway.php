<?php

namespace App\Payment\Nowpayments;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class NowpaymentsPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('nowpayments');
        $this->baseUrl = $this->credentials['sandbox'] ? 'https://api-sandbox.nowpayments.io' : 'https://api.nowpayments.io';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create payment
        $paymentData = [
            'price_amount' => (float) $amount,
            'price_currency' => strtoupper($currency),
            'pay_currency' => strtoupper($this->credentials['pay_currency'] ?? 'BTC'),
            'ipn_callback_url' => route('ipn.handle', ['gateway' => 'nowpayments']),
            'order_id' => $trxId,
            'order_description' => 'Payment for ' . setting('site_title'),
            'success_url' => route('status.success', ['trx_id' => $trxId]),
            'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
        ];

        $response = Http::withHeaders([
            'x-api-key' => $this->credentials['api_key'],
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/v1/payment', $paymentData);

        if ($response->successful()) {
            $payment = $response->json();
            return $payment['invoice_url'] ?? $payment['payment_url'];
        }

        throw new \Exception('Failed to create NOWPayments payment: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('NOWPayments IPN received', $request->all());

        try {
            // Verify IPN signature
            $receivedSignature = $request->header('x-nowpayments-sig');
            $payload = $request->getContent();
            
            if (!$this->verifySignature($payload, $receivedSignature)) {
                Log::error('NOWPayments signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = $request->all();
            $paymentStatus = $data['payment_status'] ?? '';
            $orderId = $data['order_id'] ?? '';

            if (!$orderId) {
                Log::error('NOWPayments IPN missing order_id');
                return response()->json(['error' => 'Missing order ID'], 400);
            }

            // Check if payment is successful
            if (in_array($paymentStatus, ['finished', 'partially_paid'])) {
                Transaction::completeTransaction($orderId);
                Log::info('NOWPayments payment completed for transaction: ' . $orderId);
            } else {
                Log::info('NOWPayments payment status: ' . $paymentStatus . ' for transaction: ' . $orderId);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('NOWPayments IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function verifySignature($payload, $signature): bool
    {
        if (!isset($this->credentials['ipn_secret']) || !$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha512', $payload, $this->credentials['ipn_secret']);
        return hash_equals($expectedSignature, $signature);
    }
}
