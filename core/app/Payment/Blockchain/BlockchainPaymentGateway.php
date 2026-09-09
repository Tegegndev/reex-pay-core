<?php

namespace App\Payment\Blockchain;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class BlockchainPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;
    protected $baseUrl = 'https://api.blockchain.info/';

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('blockchain');
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Create Blockchain.info payment request
        $paymentData = [
            'method' => 'create',
            'address' => $this->credentials['receive_address'],
            'callback' => route('ipn.handle', ['gateway' => 'blockchain']) . '?secret=' . $this->credentials['callback_secret'] . '&trx_id=' . $trxId,
        ];

        $response = Http::get($this->baseUrl . 'v2/receive', $paymentData);

        if ($response->successful()) {
            $result = $response->json();
            
            if (isset($result['input_address'])) {
                $inputAddress = $result['input_address'];
                
                // Store payment details for monitoring
                cache()->put('blockchain_payment_' . $trxId, [
                    'input_address' => $inputAddress,
                    'amount' => $amount,
                    'currency' => $currency
                ], 3600);
                
                // Return custom payment page with QR code and address
                return route('payment.blockchain.checkout', [
                    'trx_id' => $trxId,
                    'address' => $inputAddress,
                    'amount' => $amount,
                    'currency' => $currency
                ]);
            }
        }

        throw new \Exception('Failed to create Blockchain.info payment request: ' . $response->body());
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Blockchain.info IPN received', $request->all());

        try {
            $secret = $request->input('secret');
            $transactionId = $request->input('trx_id');
            $inputAddress = $request->input('input_address');
            $value = $request->input('value'); // Value in satoshis
            $confirmations = $request->input('confirmations', 0);

            // Verify callback secret
            if ($secret !== $this->credentials['callback_secret']) {
                Log::error('Blockchain.info IPN invalid secret');
                return response()->json(['error' => 'Invalid secret'], 400);
            }

            if (!$transactionId || !$inputAddress) {
                Log::error('Blockchain.info IPN missing required parameters');
                return response()->json(['error' => 'Missing parameters'], 400);
            }

            // Get payment details from cache
            $paymentDetails = cache()->get('blockchain_payment_' . $transactionId);
            
            if (!$paymentDetails) {
                Log::error('Blockchain.info payment details not found for transaction: ' . $transactionId);
                return response()->json(['error' => 'Payment details not found'], 400);
            }

            // Convert satoshis to BTC
            $receivedBTC = $value / 100000000;
            $requiredConfirmations = $this->credentials['required_confirmations'] ?? 1;

            // Check if payment amount is sufficient and has enough confirmations
            if ($receivedBTC >= ($paymentDetails['amount'] * 0.95) && $confirmations >= $requiredConfirmations) { // 5% tolerance
                Transaction::completeTransaction($transactionId);
                Log::info('Blockchain.info payment completed for transaction: ' . $transactionId);
                
                // Clear cache
                cache()->forget('blockchain_payment_' . $transactionId);
                
                // Return success to Blockchain.info
                return response()->json(['success' => true]);
            } else {
                Log::info('Blockchain.info payment insufficient or pending confirmations: ' . $confirmations . '/' . $requiredConfirmations . ' for transaction: ' . $transactionId);
            }

            return response()->json(['status' => 'pending']);

        } catch (\Exception $e) {
            Log::error('Blockchain.info IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
