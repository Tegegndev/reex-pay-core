<?php

namespace App\Payment\Blockio;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class BlockioPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl = 'https://block.io/api/v2/';

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('blockio');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create new address for this payment
        $addressResponse = Http::get($this->baseUrl . 'get_new_address/', [
            'api_key' => $this->credentials['api_key'],
            'label' => 'payment_' . $trxId,
        ]);

        if ($addressResponse->successful()) {
            $addressData = $addressResponse->json();
            
            if ($addressData['status'] === 'success') {
                $address = $addressData['data']['address'];
                
                // Store address for monitoring
                cache()->put('blockio_address_' . $trxId, $address, 3600);
                
                // Return a custom payment page URL with QR code and address
                return route('payment.blockio.checkout', [
                    'trx_id' => $trxId,
                    'address' => $address,
                    'amount' => $amount,
                    'currency' => $currency
                ]);
            }
        }

        throw new \Exception('Failed to create Block.io payment address: ' . $addressResponse->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Block.io IPN received', $request->all());

        try {
            $transactionId = $request->input('transaction_id');
            $address = $request->input('address');
            $amount = $request->input('amount');
            $confirmations = $request->input('confirmations', 0);

            if (!$transactionId || !$address) {
                Log::error('Block.io IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Verify transaction with Block.io API
            $balanceResponse = Http::get($this->baseUrl . 'get_address_balance/', [
                'api_key' => $this->credentials['api_key'],
                'addresses' => $address,
            ]);

            if ($balanceResponse->successful()) {
                $balanceData = $balanceResponse->json();
                
                if ($balanceData['status'] === 'success') {
                    $addressBalance = $balanceData['data']['available_balance'];
                    
                    // Check if sufficient confirmations (default 1)
                    $requiredConfirmations = $this->credentials['required_confirmations'] ?? 1;
                    
                    if ($confirmations >= $requiredConfirmations && $addressBalance > 0) {
                        Transaction::completeTransaction($transactionId);
                        Log::info('Block.io payment completed for transaction: ' . $transactionId);
                    } else {
                        Log::info('Block.io payment pending confirmations: ' . $confirmations . '/' . $requiredConfirmations . ' for transaction: ' . $transactionId);
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Block.io IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
