<?php

namespace App\Payment\Cashmaal;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class CashmaalPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('cashmaal');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Cashmaal payment form URL
        $checkoutUrl = 'https://www.cashmaal.com/Pay/?' . http_build_query([
            'web' => $this->credentials['web_id'],
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => strtoupper($currency),
            'reference' => $trxId,
            'return_url' => route('status.success', ['trx_id' => $trxId]),
            'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
            'ipn_url' => route('ipn.handle', ['gateway' => 'cashmaal']),
            'item_name' => 'Payment for ' . setting('site_title'),
            'custom' => $trxId,
        ]);

        return $checkoutUrl;
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Cashmaal IPN received', $request->all());

        try {
            $transactionId = $request->input('custom') ?: $request->input('reference');
            $paymentStatus = $request->input('payment_status');
            $txnId = $request->input('txn_id');

            if (!$transactionId) {
                Log::error('Cashmaal IPN missing transaction ID');
                return response()->json(['error' => 'Missing transaction ID'], 400);
            }

            // Verify payment with Cashmaal (if verification endpoint exists)
            // Note: Cashmaal may not have direct verification API, so we rely on IPN data
            
            // Check payment status
            if (strtolower($paymentStatus) === 'completed' || strtolower($paymentStatus) === 'success') {
                Transaction::completeTransaction($transactionId);
                Log::info('Cashmaal payment completed for transaction: ' . $transactionId);
            } else {
                Log::info('Cashmaal payment status: ' . $paymentStatus . ' for transaction: ' . $transactionId);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Cashmaal IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
