<?php

namespace App\Payment\Coinpayments;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class CoinpaymentsPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('coinpayments');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // CoinPayments transaction parameters
        $params = [
            'version' => 1,
            'cmd' => 'create_transaction',
            'amount' => number_format($amount, 8, '.', ''),
            'currency1' => strtoupper($currency),
            'currency2' => strtoupper($this->credentials['currency2'] ?? 'BTC'),
            'buyer_email' => auth()->user()->email ?? 'customer@example.com',
            'buyer_name' => auth()->user()->name ?? 'Customer',
            'item_name' => 'Payment for ' . setting('site_title'),
            'item_number' => $trxId,
            'invoice' => $trxId,
            'custom' => $trxId,
            'ipn_url' => route('ipn.handle', ['gateway' => 'coinpayments']),
            'success_url' => route('status.success', ['trx_id' => $trxId]),
            'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
        ];

        $params['key'] = $this->credentials['public_key'];
        $params['format'] = 'json';

        // Create HMAC signature
        $post_data = http_build_query($params, '', '&');
        $hmac = hash_hmac('sha512', $post_data, $this->credentials['private_key']);

        $response = Http::withHeaders([
            'HMAC' => $hmac,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://www.coinpayments.net/api.php', $params);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['error'] === 'ok') {
                return $result['result']['checkout_url'];
            } else {
                throw new \Exception('CoinPayments API Error: ' . $result['error']);
            }
        }

        throw new \Exception('Failed to create CoinPayments transaction: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('CoinPayments IPN received', $request->all());

        try {
            // Verify IPN signature
            if (!$this->verifyIPN($request)) {
                Log::error('CoinPayments IPN signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $transactionId = $request->input('custom') ?: $request->input('invoice');
            $status = (int) $request->input('status');
            $statusText = $request->input('status_text');

            if (!$transactionId) {
                Log::error('CoinPayments IPN missing transaction ID');
                return response()->json(['error' => 'Missing transaction ID'], 400);
            }

            // Status >= 100 means payment is complete
            // Status >= 2 means payment received (for crypto currencies)
            if ($status >= 100 || $status >= 2) {
                Transaction::completeTransaction($transactionId);
                Log::info('CoinPayments payment completed for transaction: ' . $transactionId . ', Status: ' . $statusText);
            } else {
                Log::info('CoinPayments payment pending for transaction: ' . $transactionId . ', Status: ' . $statusText);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('CoinPayments IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function verifyIPN(Request $request): bool
    {
        $headers = $request->headers->all();
        $hmac = $headers['hmac'][0] ?? '';
        
        if (empty($hmac)) {
            return false;
        }

        $payload = $request->getContent();
        $calculated_hmac = hash_hmac('sha512', $payload, $this->credentials['ipn_secret']);

        return hash_equals($calculated_hmac, $hmac);
    }
}
