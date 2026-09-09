<?php

namespace App\Payment\Moneroo;

use App\Payment\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonerooPaymentGateway implements PaymentGateway
{
    /** @var array */
    protected $credentials;

    public function __construct()
    {
        $this->credentials = \App\Models\PaymentGateway::getCredentials('moneroo');

        // Ensure your Moneroo keys are present; throw if missing
        if (empty($this->credentials['api_key']) || empty($this->credentials['api_secret'])) {
            throw new \RuntimeException('Moneroo API credentials are not configured.');
        }
    }

    /**
     * Initiate a deposit/payment with Moneroo.
     *
     * @param int    $amount
     * @param string $currency
     * @param string $trxId
     * @return RedirectResponse
     */
    public function deposit($amount, $currency, $trxId)
    {
        $url = 'https://api.moneroo.io/v1/payments/initialize';

        $data = [
            'amount'      => $amount,
            'currency'    => $currency,
            'description' => "Deposit for transaction {$trxId}",
            'return_url'  => route('status.callback', ['gateway' => 'moneroo', 'trx' => $trxId]),
            'metadata'    => [
                'trx_id' => $trxId,
                'source' => 'wallet-deposit',
            ],
        ];

        if (auth()->check()) {
            $user = auth()->user();
            $data['customer'] = [
                'email'      => $user->email,
                'first_name' => $user->first_name ?? '',
                'last_name'  => $user->last_name ?? '',
            ];
        }

        try {
            // Use secret key for all server‑side calls
            $response = Http::withToken($this->credentials['api_key'])
                ->acceptJson()
                ->post($url, $data);

            if ($response->status() !== 201) {
                Log::error('Moneroo payment init error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'trxId'  => $trxId,
                ]);
                return back()->withErrors(['error' => 'Failed to initialize payment with Moneroo.']);
            }

            $checkoutUrl = data_get($response->json(), 'data.checkout_url');
            if (! $checkoutUrl) {
                Log::error('Moneroo payment missing checkout_url', ['response' => $response->json()]);
                return back()->withErrors(['error' => 'Missing checkout URL from Moneroo.']);
            }
            return $checkoutUrl;
        } catch (\Throwable $e) {
            Log::error('Moneroo payment init exception', ['message' => $e->getMessage(), 'trxId' => $trxId]);
            return back()->withErrors(['error' => 'An unexpected error occurred while initiating payment.']);
        }
    }

    /**
     * Initiate a payout/withdrawal with Moneroo.
     *
     * @param int    $amount
     * @param string $currency
     * @param string $trxId             Unique reference for your withdrawal
     * @param array  $withdrawCredential Contains at least 'method' and 'recipient' (method‑specific fields)
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function withdraw($amount, $currency, $trxId, array $withdrawCredential)
    {
        $url = 'https://api.moneroo.io/v1/payouts/initialize';

        // Validate that method and recipient are provided
        if (empty($withdrawCredential['method']) || empty($withdrawCredential['recipient'])) {
            return back()->withErrors(['error' => 'Missing payout method or recipient details.']);
        }

        // Build the payout payload based on Moneroo docs:contentReference[oaicite:3]{index=3}.
        $data = [
            'amount'      => (int) $amount,
            'currency'    => $currency,
            'description' => "Withdrawal for transaction {$trxId}",
            'method'      => $withdrawCredential['method'],
            'recipient'   => $withdrawCredential['recipient'], // e.g. ['msisdn' => '22951345020']:contentReference[oaicite:4]{index=4}
            'metadata'    => [
                'trx_id' => $trxId,
                'source' => 'wallet-withdraw',
            ],
        ];

        // Include customer details as required by the API:contentReference[oaicite:5]{index=5}.
        if (auth()->check()) {
            $user = auth()->user();
            $data['customer'] = [
                'email'      => $user->email,
                'first_name' => $user->first_name ?? '',
                'last_name'  => $user->last_name ?? '',
                // Add phone/address fields here if required by your payout method.
            ];
        }

        try {
            $response = Http::withToken($this->credentials['api_key'])
                ->acceptJson()
                ->post($url, $data);

            // Successful initialization typically returns 201 or a success flag:contentReference[oaicite:6]{index=6}.
            if (! $response->successful()) {
                Log::error('Moneroo payout init error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'trxId'  => $trxId,
                ]);
                return back()->withErrors(['error' => 'Failed to initialize payout with Moneroo.']);
            }

            $payoutId = data_get($response->json(), 'data.id');
            if (! $payoutId) {
                Log::error('Moneroo payout missing ID', ['response' => $response->json()]);
                return back()->withErrors(['error' => 'Missing payout ID from Moneroo.']);
            }

            // At this point, the payout is initiated. You may store $payoutId
            // and show a success message. The final status will come via webhook.
            return back()->with('message', 'Payout initiated successfully.');
        } catch (\Throwable $e) {
            Log::error('Moneroo payout init exception', ['message' => $e->getMessage(), 'trxId' => $trxId]);
            return back()->withErrors(['error' => 'An unexpected error occurred while initiating payout.']);
        }
    }

    /**
     * Handle webhook/IPN notifications from Moneroo.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleIPN(Request $request)
    {
        $secret = $this->credentials['webhook_signing_secret'] ?? null;

        if (! $secret) {
            Log::warning('Moneroo webhook secret missing');
            http_response_code(403);
            exit('Webhook signing secret not configured.');
        }

        // Get raw payload and compute signature
        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $secret);
        $receivedSignature = $_SERVER['HTTP_X_MONEROO_SIGNATURE'] ?? '';

        // Strict signature verification
        if (! hash_equals($signature, $receivedSignature)) {
            Log::warning('Invalid Moneroo webhook signature', [
                'computed' => $signature,
                'received' => $receivedSignature
            ]);
            http_response_code(403);
            exit('Invalid webhook signature');
        }

        // Process webhook data
        $webhookData = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in Moneroo webhook payload');
            http_response_code(400);
            exit('Invalid JSON payload');
        }

        $event = $webhookData['event'] ?? null;
        $data = $webhookData['data'] ?? [];
        $transactionId = $data['id'] ?? null;
        $refTrxId = $data['metadata']['trx_id'] ?? null;

        // Handle payment and payout events
        switch ($event) {
            case 'payment.success':
                if ($data['metadata']['trx_id'] && $this->verifyTransaction($transactionId)['status'] === 'success') {
                    \Transaction::completeTransaction($refTrxId);
                }
                break;

            case 'payment.failed':
            case 'payout.failed':
            case 'payment.cancelled':
                if ($transactionId) {
                    \Transaction::failTransaction($refTrxId);
                }
                break;

            case 'payout.success':
                if ($transactionId && $this->verifyPayout($transactionId)['status'] === 'success') {
                    \Transaction::completeTransaction($refTrxId);
                }
                break;

            // Log unhandled events for debugging
            default:
                Log::info('Unhandled Moneroo webhook event', ['event' => $event, 'data' => $data]);
                break;
        }

        // Send successful response
        http_response_code(200);
        exit('OK');
    }

    /**
     * Verify a payment transaction with Moneroo’s verify endpoint.
     *
     * @param string $paymentId
     * @return array|null
     */
    protected function verifyTransaction(string $paymentId): ?array
    {
        try {
            $response = Http::withToken($this->credentials['api_key'])
                ->acceptJson()
                ->get("https://api.moneroo.io/v1/payments/{$paymentId}/verify");

            return $response->successful() ? $response->json('data') : null;
        } catch (\Throwable $e) {
            Log::error('Moneroo verify transaction exception', [
                'paymentId' => $paymentId,
                'message'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verify a payout transaction with Moneroo’s verify endpoint.
     *
     * @param string $payoutId
     * @return array|null
     */
    protected function verifyPayout(string $payoutId): ?array
    {
        try {
            $response = Http::withToken($this->credentials['api_secret'])
                ->acceptJson()
                ->get("https://api.moneroo.io/v1/payouts/{$payoutId}/verify");

            return $response->successful() ? $response->json('data') : null;
        } catch (\Throwable $e) {
            Log::error('Moneroo verify payout exception', [
                'payoutId' => $payoutId,
                'message'  => $e->getMessage(),
            ]);
            return null;
        }
    }
}
