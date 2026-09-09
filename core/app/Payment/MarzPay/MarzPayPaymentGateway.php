<?php

namespace App\Payment\MarzPay;

use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Transaction;

class MarzPayPaymentGateway implements PaymentGatewayInterface
{
    private const BASE_URL = 'https://wallet.wearemarz.com/api/v1';

    protected array $credentials;

    protected string $apiKey;

    protected string $apiSecret;

    protected ?string $webhookSecret;

    protected string $defaultCountry;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('marzpay');
        $this->apiKey = (string) ($this->credentials['api_key'] ?? '');
        $this->apiSecret = (string) ($this->credentials['api_secret'] ?? '');
        $this->webhookSecret = ! empty($this->credentials['webhook_secret']) ? (string) $this->credentials['webhook_secret'] : null;
        $this->defaultCountry = strtoupper((string) ($this->credentials['country'] ?? 'UG'));
    }

    /**
     * Resolve the ISO country code based on currency or request/configuration.
     */
    protected function resolveCountry(string $currency): string
    {
        return match (strtoupper($currency)) {
            'KES'   => 'KE',
            'RWF'   => 'RW',
            'CDF'   => 'CD',
            'UGX'   => 'UG',
            default => $this->defaultCountry ?: 'UG',
        };
    }

    /**
     * Format phone number to E.164 without leading zeros or spaces.
     */
    protected function formatPhone(?string $phone, string $country): ?string
    {
        if (! $phone) {
            return null;
        }

        $clean = preg_replace('/[^\d+]/', '', $phone);
        if (str_starts_with($clean, '+')) {
            return $clean;
        }

        return match ($country) {
            'UG' => str_starts_with($clean, '0') ? '+256'.substr($clean, 1) : (str_starts_with($clean, '256') ? '+'.$clean : '+256'.$clean),
            'KE' => str_starts_with($clean, '0') ? '+254'.substr($clean, 1) : (str_starts_with($clean, '254') ? '+'.$clean : '+254'.$clean),
            'RW' => str_starts_with($clean, '0') ? '+250'.substr($clean, 1) : (str_starts_with($clean, '250') ? '+'.$clean : '+250'.$clean),
            'CD' => str_starts_with($clean, '0') ? '+243'.substr($clean, 1) : (str_starts_with($clean, '243') ? '+'.$clean : '+243'.$clean),
            default => '+'.$clean,
        };
    }

    /**
     * Make an authenticated HTTP request to the MarzPay API.
     */
    protected function request(string $method, string $endpoint, array $payload = [])
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw new Exception('MarzPay API credentials (api_key, api_secret) are not configured.');
        }

        $url = self::BASE_URL.'/'.ltrim($endpoint, '/');

        return Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->acceptJson()
            ->contentType('application/json')
            ->connectTimeout(15)
            ->timeout(30)
            ->{$method}($url, $payload);
    }

    /**
     * Initiate a deposit via MarzPay.
     * Supports both direct Mobile Money prompt and Hosted Payment Link checkout with Sandbox auto-detection.
     */
    public function deposit($amount, $currency, $trxId)
    {
        $country = $this->resolveCountry($currency);
        $reference = (string) Str::uuid();
        $callbackUrl = route('ipn.handle', ['gateway' => 'marzpay']);

        // Check if user has a phone number
        $user = auth()->user();
        $userPhone = request('credentials.phone_number') ?? request('phone') ?? $user?->phone;
        $formattedPhone = $this->formatPhone($userPhone, $country);

        // Map transaction in cache for reference lookup
        cache()->put("marzpay_reference_{$reference}", $trxId, now()->addDays(2));

        // 1. If phone number is available, initiate direct mobile collection
        if ($formattedPhone) {
            $payload = [
                'amount'       => (float) $amount,
                'phone_number' => $formattedPhone,
                'country'      => $country,
                'currency'     => strtoupper((string) $currency),
                'reference'    => $reference,
                'description'  => 'Deposit for Order #'.$trxId,
                'callback_url' => $callbackUrl,
                'metadata'     => [
                    ['trx_id' => $trxId],
                ],
            ];

            $response = $this->request('post', '/collect-money', $payload);

            if ($response->successful()) {
                session()->put('cancel_tnx', $trxId);

                // Check for Sandbox Mode
                $isSandbox = $response->json('data.metadata.sandbox_mode') === true
                    || $response->json('data.transaction.status') === 'sandbox'
                    || str_contains(strtolower((string) $response->json('message')), 'sandbox');

                if ($isSandbox) {
                    Log::info("MarzPay Sandbox Deposit auto-completed for TRX #{$trxId}");
                    Transaction::completeTransaction($trxId);
                    notifyEvs('success', __('Deposit Successful (Sandbox Mode)'));
                    return route('status.success', ['trx_id' => $trxId]);
                }

                return route('status.callback', [
                    'gateway' => 'marzpay',
                    'trx'     => $trxId,
                ]);
            }

            $errorMsg = $response->json('message') ?? 'MarzPay collection failed (HTTP '.$response->status().')';
            Log::error('MarzPay Direct Collection Error', ['response' => $response->json(), 'status' => $response->status()]);
            throw new Exception($errorMsg);
        }

        // 2. Otherwise create Hosted Payment Link
        $linkPayload = [
            'title'        => setting('site_title', 'ReexPay').' Deposit',
            'description'  => 'Deposit for Transaction #'.$trxId,
            'amount'       => (float) $amount,
            'currency'     => strtoupper((string) $currency),
            'country'      => $country,
            'callback_url' => $callbackUrl,
            'return_url'   => route('status.success', ['trx_id' => $trxId]),
            'metadata'     => [
                ['trx_id' => $trxId],
            ],
        ];

        $response = $this->request('post', '/payment-links', $linkPayload);

        if ($response->successful() && $response->json('data.payment_link.url')) {
            session()->put('cancel_tnx', $trxId);
            return $response->json('data.payment_link.url');
        }

        $errorMsg = $response->json('message') ?? 'MarzPay could not initiate payment request (HTTP '.$response->status().').';
        Log::error('MarzPay Payment Link Error', ['response' => $response->json(), 'status' => $response->status()]);
        throw new Exception($errorMsg);
    }

    /**
     * Process a withdrawal (payout / disbursement).
     */
    public function withdraw($amount, $currency, $trxId, $withdrawCredential)
    {
        $country = $this->resolveCountry($currency);
        $reference = (string) Str::uuid();
        $formattedPhone = $this->formatPhone($withdrawCredential, $country);
        $callbackUrl = route('ipn.handle', ['gateway' => 'marzpay']);

        if (! $formattedPhone) {
            throw new Exception("A valid mobile money phone number is required for {$country} withdrawal.");
        }

        cache()->put("marzpay_reference_{$reference}", $trxId, now()->addDays(2));

        $payload = [
            'amount'       => (float) $amount,
            'phone_number' => $formattedPhone,
            'country'      => $country,
            'currency'     => strtoupper((string) $currency),
            'reference'    => $reference,
            'description'  => 'Withdrawal payout #'.$trxId,
            'callback_url' => $callbackUrl,
            'metadata'     => [
                ['trx_id' => $trxId],
            ],
        ];

        $response = $this->request('post', '/send-money', $payload);

        if ($response->successful()) {
            $isSandbox = $response->json('data.metadata.sandbox_mode') === true
                || $response->json('data.transaction.status') === 'sandbox'
                || str_contains(strtolower((string) $response->json('message')), 'sandbox');

            if ($isSandbox) {
                Log::info("MarzPay Sandbox Withdrawal auto-completed for TRX #{$trxId}");
                Transaction::completeTransaction($trxId);
            }
            return;
        }

        $msg = $response->json('message') ?? 'MarzPay withdrawal request failed (HTTP '.$response->status().').';
        Log::error('MarzPay Withdrawal Error', ['response' => $response->json()]);
        throw new Exception($msg);
    }

    /**
     * Handle incoming MarzPay IPN / Webhooks.
     */
    public function handleIPN(Request $request)
    {
        // 1. Verify webhook signature if secret configured
        $signature = $request->header('X-MarzPay-Signature');
        if ($this->webhookSecret && $signature) {
            $computed = hash_hmac('sha256', $request->getContent(), $this->webhookSecret);
            if (! hash_equals($computed, $signature)) {
                Log::warning('MarzPay IPN: Invalid signature', ['received' => $signature, 'computed' => $computed]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $eventType = $request->input('event_type') ?? $request->input('data.event_type');
        $payload = $request->all();
        Log::info("MarzPay IPN Received: {$eventType}", $payload);

        // Extract transaction ID from metadata or reference cache
        $trxId = $this->extractTrxId($request);

        if (! $trxId) {
            Log::warning('MarzPay IPN: Could not resolve transaction ID', $payload);
            return response()->json(['status' => 'ignored', 'reason' => 'Transaction ID not found'], 200);
        }

        switch ($eventType) {
            case 'collection.completed':
            case 'collection.success':
            case 'payment.success':
            case 'disbursement.completed':
            case 'disbursement.success':
            case 'withdrawal.completed':
                Transaction::completeTransaction($trxId);
                break;

            case 'collection.failed':
            case 'disbursement.failed':
            case 'withdrawal.failed':
                $failureReason = $request->input('transaction.failure_reason') ?? $request->input('message') ?? 'MarzPay transaction failed';
                Transaction::cancelTransaction($trxId, $failureReason, true);
                break;

            default:
                Log::info("MarzPay IPN: Event {$eventType} unhandled or pending.");
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Helper to extract local trx_id from webhook payload.
     */
    protected function extractTrxId(Request $request): ?string
    {
        // Check top-level metadata array
        $metadata = $request->input('metadata') ?? $request->input('data.metadata') ?? [];
        if (is_array($metadata)) {
            foreach ($metadata as $item) {
                if (is_array($item)) {
                    if (isset($item['trx_id'])) return (string) $item['trx_id'];
                    if (isset($item['digikash_transaction'])) return (string) $item['digikash_transaction'];
                    if (isset($item['transaction_id'])) return (string) $item['transaction_id'];
                }
            }
        }

        // Check reference in transaction object
        $reference = $request->input('transaction.reference')
            ?? $request->input('reference')
            ?? $request->input('data.transaction.reference');

        if ($reference && cache()->has("marzpay_reference_{$reference}")) {
            return (string) cache()->get("marzpay_reference_{$reference}");
        }

        return null;
    }
}
