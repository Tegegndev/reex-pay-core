<?php

namespace App\Payment\Razorpay;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Transaction;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    protected $credentials;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('razorpay');
    }

    public function deposit($amount, $currency, $trxId)
    {
        $orderData = [
            'amount' => $amount * 100, // Razorpay expects amount in paise
            'currency' => $currency,
            'receipt' => $trxId,
            'notes' => [
                'transaction_id' => $trxId,
                'payment_for' => setting('site_title'),
            ],
        ];

        $response = Http::withBasicAuth($this->credentials['key_id'], $this->credentials['key_secret'])
            ->post('https://api.razorpay.com/v1/orders', $orderData);

        if ($response->successful()) {
            $order = $response->json();
            
            // Generate Razorpay checkout URL
            $checkoutUrl = $this->generateCheckoutUrl($order, $amount, $currency, $trxId);
            
            return $checkoutUrl;
        }

        throw new \Exception('Failed to create Razorpay order: ' . $response->body());
    }

    private function generateCheckoutUrl($order, $amount, $currency, $trxId)
    {
        $checkoutData = [
            'key' => $this->credentials['key_id'],
            'amount' => $amount * 100,
            'currency' => $currency,
            'name' => setting('site_title'),
            'description' => 'Payment for ' . setting('site_title'),
            'order_id' => $order['id'],
            'callback_url' => route('ipn.handle', ['gateway' => 'razorpay']),
            'cancel_url' => route('status.cancel', ['trx_id' => $trxId]),
            'notes' => [
                'transaction_id' => $trxId,
            ],
            'theme' => [
                'color' => '#F37254'
            ],
        ];

        // Create a temporary form submission page
        $form = '<form action="https://api.razorpay.com/v1/checkout/embedded" method="POST" id="razorpay-form">';
        foreach ($checkoutData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $form .= '<input type="hidden" name="' . $key . '[' . $subKey . ']" value="' . $subValue . '">';
                }
            } else {
                $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
            }
        }
        $form .= '</form>';
        $form .= '<script>document.getElementById("razorpay-form").submit();</script>';

        // In a real implementation, you'd want to return a proper redirect URL
        // For now, we'll use Razorpay's standard checkout
        return 'https://checkout.razorpay.com/v1/checkout.js?' . http_build_query([
            'key_id' => $this->credentials['key_id'],
            'amount' => $amount * 100,
            'currency' => $currency,
            'order_id' => $order['id'],
            'callback_url' => route('ipn.handle', ['gateway' => 'razorpay']),
        ]);
    }

    public function handleIPN(Request $request): JsonResponse
    {
        Log::info('Razorpay IPN received', $request->all());

        try {
            // Verify the payment signature
            $signature = $request->input('razorpay_signature');
            $paymentId = $request->input('razorpay_payment_id');
            $orderId = $request->input('razorpay_order_id');

            if (!$this->verifySignature($paymentId, $orderId, $signature)) {
                Log::error('Razorpay signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Fetch payment details from Razorpay
            $paymentResponse = Http::withBasicAuth($this->credentials['key_id'], $this->credentials['key_secret'])
                ->get('https://api.razorpay.com/v1/payments/' . $paymentId);

            if (!$paymentResponse->successful()) {
                Log::error('Failed to fetch Razorpay payment details');
                return response()->json(['error' => 'Payment verification failed'], 400);
            }

            $payment = $paymentResponse->json();

            // Check if payment is captured/successful
            if ($payment['status'] === 'captured') {
                $transactionId = $payment['notes']['transaction_id'] ?? null;
                
                if ($transactionId) {
                    Transaction::completeTransaction($transactionId);
                    Log::info('Razorpay payment completed for transaction: ' . $transactionId);
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Razorpay IPN error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function verifySignature($paymentId, $orderId, $signature): bool
    {
        $body = $orderId . '|' . $paymentId;
        $expectedSignature = hash_hmac('sha256', $body, $this->credentials['key_secret']);
        
        return hash_equals($expectedSignature, $signature);
    }
}
