<?php

namespace App\Payment\Voguepay;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class VoguepayPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('voguepay');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Voguepay checkout URL with parameters
        $checkoutUrl = 'https://voguepay.com/pay/?' . http_build_query([
            'p' => $this->credentials['merchant_id'],
            'pay_id' => $trxId,
            'price' => $amount,
            'cur' => $currency,
            'merchant_ref' => $trxId,
            'memo' => 'Payment for ' . setting('site_title'),
            'notify_url' => route('ipn.handle', ['gateway' => 'voguepay']),
            'success_url' => route('status.success', ['trx_id' => $trxId]),
            'fail_url' => route('status.cancel', ['trx_id' => $trxId]),
            'developer_code' => '5a61aca296c05',
        ]);

        return $checkoutUrl;
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Voguepay IPN received', $request->all());

        try {
            $transactionId = $request->input('merchant_ref');
            $voguepayTxnId = $request->input('transaction_id');
            $status = $request->input('status');

            if (!$transactionId || !$voguepayTxnId) {
                Log::error('Voguepay IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Verify transaction with Voguepay API
            $verificationResponse = Http::get('https://voguepay.com/api/', [
                'v_transaction_id' => $voguepayTxnId,
                'type' => 'json'
            ]);

            if (!$verificationResponse->successful()) {
                Log::error('Voguepay transaction verification failed');
                return response()->json(['error' => 'Verification failed'], 400);
            }

            $verification = $verificationResponse->json();

            // Check if transaction is successful
            if ($verification['status'] === 'Approved' && $status === 'Approved') {
                Transaction::completeTransaction($transactionId);
                Log::info('Voguepay payment completed for transaction: ' . $transactionId);
            } else {
                Log::info('Voguepay payment failed for transaction: ' . $transactionId . ', Status: ' . $status);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Voguepay IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
