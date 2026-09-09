<?php

namespace App\Payment\Bitpayserver;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class BitpayserverPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('bitpayserver');
        $this->baseUrl = rtrim($this->credentials['server_url'], '/');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create BTCPay Server invoice
        $invoiceData = [
            'amount' => (float) $amount,
            'currency' => strtoupper($currency),
            'orderId' => $trxId,
            'notificationURL' => route('ipn.handle', ['gateway' => 'bitpayserver']),
            'redirectURL' => route('status.success', ['trx_id' => $trxId]),
            'itemDesc' => 'Payment for ' . setting('site_title'),
            'itemCode' => $trxId,
            'physical' => false,
            'buyer' => [
                'name' => auth()->user()->name ?? 'Customer',
                'email' => auth()->user()->email ?? 'customer@example.com',
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'token ' . $this->credentials['api_token'],
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/api/v1/invoices', $invoiceData);

        if ($response->successful()) {
            $invoice = $response->json();
            return $invoice['url'];
        }

        throw new \Exception('Failed to create BTCPay Server invoice: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('BTCPay Server IPN received', $request->all());

        try {
            $invoiceId = $request->input('id');
            $orderId = $request->input('orderId');
            $status = $request->input('status');

            if (!$invoiceId || !$orderId) {
                Log::error('BTCPay Server IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Verify invoice with BTCPay Server API
            $verificationResponse = Http::withHeaders([
                'Authorization' => 'token ' . $this->credentials['api_token'],
            ])->get($this->baseUrl . '/api/v1/invoices/' . $invoiceId);

            if ($verificationResponse->successful()) {
                $invoice = $verificationResponse->json();
                
                // Check if invoice is paid (confirmed or complete)
                if (in_array($invoice['status'], ['paid', 'confirmed', 'complete'])) {
                    Transaction::completeTransaction($orderId);
                    Log::info('BTCPay Server payment completed for transaction: ' . $orderId);
                } else {
                    Log::info('BTCPay Server payment status: ' . $invoice['status'] . ' for transaction: ' . $orderId);
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('BTCPay Server IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
