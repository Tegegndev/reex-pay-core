<?php

namespace App\Payment\Binance;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class BinancePaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('binance');
        $this->baseUrl = $this->credentials['sandbox'] 
            ? 'https://bpay.binanceapi.com' 
            : 'https://bpay.binanceapi.com';
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create Binance Pay order
        $timestamp = time() * 1000;
        $nonce = uniqid();
        
        $orderData = [
            'env' => [
                'terminalType' => 'WEB'
            ],
            'merchantTradeNo' => $trxId,
            'orderAmount' => (float) $amount,
            'currency' => strtoupper($currency),
            'goods' => [
                'goodsType' => '01',
                'goodsCategory' => 'Z000',
                'referenceGoodsId' => $trxId,
                'goodsName' => 'Payment for ' . setting('site_title'),
                'goodsDetail' => 'Payment transaction #' . $trxId
            ],
            'returnUrl' => route('status.success', ['trx_id' => $trxId]),
            'cancelUrl' => route('status.cancel', ['trx_id' => $trxId]),
            'webhookUrl' => route('ipn.handle', ['gateway' => 'binance'])
        ];

        $payload = json_encode($orderData);
        $signature = $this->generateSignature('POST', '/binancepay/openapi/v2/order', $payload, $timestamp, $nonce);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => $nonce,
            'BinancePay-Certificate-SN' => $this->credentials['certificate_sn'],
            'BinancePay-Signature' => $signature,
        ])->post($this->baseUrl . '/binancepay/openapi/v2/order', $orderData);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['status'] === 'SUCCESS') {
                return $result['data']['checkoutUrl'];
            } else {
                throw new \Exception('Binance Pay API Error: ' . $result['errorMessage']);
            }
        }

        throw new \Exception('Failed to create Binance Pay order: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Binance Pay IPN received', $request->all());

        try {
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($request)) {
                Log::error('Binance Pay webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = $request->all();
            $merchantTradeNo = $data['merchantTradeNo'] ?? '';
            $orderStatus = $data['orderStatus'] ?? '';

            if (!$merchantTradeNo) {
                Log::error('Binance Pay IPN missing merchantTradeNo');
                return response()->json(['error' => 'Missing transaction ID'], 400);
            }

            // Check if payment is successful
            if ($orderStatus === 'SUCCESS') {
                Transaction::completeTransaction($merchantTradeNo);
                Log::info('Binance Pay payment completed for transaction: ' . $merchantTradeNo);
            } else {
                Log::info('Binance Pay payment status: ' . $orderStatus . ' for transaction: ' . $merchantTradeNo);
            }

            return response()->json(['returnCode' => 'SUCCESS', 'returnMessage' => null]);

        } catch (\Exception $e) {
            Log::error('Binance Pay IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function generateSignature($method, $path, $body, $timestamp, $nonce): string
    {
        $payload = $timestamp . "\n" . $nonce . "\n" . $body . "\n";
        $privateKey = $this->credentials['private_key'];
        
        $key = openssl_pkey_get_private($privateKey);
        openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256);
        
        return base64_encode($signature);
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        $timestamp = $request->header('BinancePay-Timestamp');
        $nonce = $request->header('BinancePay-Nonce');
        $signature = $request->header('BinancePay-Signature');
        $certificateSn = $request->header('BinancePay-Certificate-SN');
        
        if (!$timestamp || !$nonce || !$signature) {
            return false;
        }

        $payload = $timestamp . "\n" . $nonce . "\n" . $request->getContent() . "\n";
        
        // Verify with Binance public key (you need to implement this based on your certificate)
        // For now, returning true - implement proper verification in production
        return true;
    }
}
