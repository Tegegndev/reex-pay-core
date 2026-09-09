<?php

namespace App\Payment\MarzPay;

use App\Enums\TrxType;
use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Transaction;

class MarzPayPaymentGateway implements PaymentGatewayInterface
{
    private const BASE_URL = 'https://wallet.wearemarz.com/api/v1';

    private array $credentials;

    public function __construct()
    {
        $this->credentials = PaymentGateway::getCredentials('marzpay');

        if (
            empty($this->credentials['api_key']) ||
            empty($this->credentials['api_secret'])
        ) {
            throw new Exception(
                'MarzPay payment credentials are not configured by the administrator.'
            );
        }
    }

    /**
     * Create a MarzPay mobile-money collection.
     */
    public function deposit($amount, $currency, $trxId)
    {
        $country = strtoupper(
            (string) ($this->credentials['country'] ?? 'UG')
        );

        $rawPhone = auth()->user()?->phone;
        $phone = $this->formatPhone($rawPhone, $country);

        if (! $phone) {
            throw new Exception(
                "Add a valid {$country} mobile-money phone number before depositing."
            );
        }

        $callbackUrl = $this->callbackUrl();

        /*
         * MarzPay requires a unique UUID v4 reference.
         */
        $reference = (string) Str::uuid();

        $payload = [
            'amount'       => (float) $amount,
            'phone_number' => $phone,
            'country'      => $country,
            'currency'     => strtoupper((string) $currency),
            'reference'    => $reference,
            'description'  => 'MarzPay wallet deposit',
            'callback_url' => $callbackUrl,
            'metadata'     => [
                [
                    'digikash_transaction' => $trxId,
                ],
            ],
        ];

        $response = $this->request(
            'post',
            '/collect-money',
            $payload
        );

        /*
         * Safe debug logging.
         *
         * Never log API credentials or the customer's phone number.
         */
        Log::info('MARZPAY COLLECTION DEBUG', [
            'http_status' => $response->status(),
            'api_status' => $response->json('status'),
            'message' => $response->json('message'),

            'transaction_status' => $response->json(
                'data.transaction.status'
            ),

            'provider' => $response->json(
                'data.collection.provider'
            ),

            'mode' => $response->json(
                'data.collection.mode'
            ),

            'provider_transaction_id' => $response->json(
                'data.collection.provider_transaction_id'
            ),

            'reference' => $reference,
            'local_transaction' => $trxId,

            'country' => $country,
            'currency' => strtoupper((string) $currency),

            'callback_host' => parse_url(
                $callbackUrl,
                PHP_URL_HOST
            ),
        ]);

        if (! $this->accepted($response)) {
            throw new Exception(
                'MarzPay deposit request failed: ' .
                (
                    $response->json('message')
                    ?? 'unknown provider error'
                )
            );
        }

        /*
         * Save the provider reference so the webhook can still
         * find our local transaction if metadata is unavailable.
         */
        cache()->put(
            "marzpay_reference_{$reference}",
            $trxId,
            now()->addDay()
        );

        /*
         * MarzPay triggers the mobile-money prompt itself.
         * This URL only returns the user to our local status page.
         */
        return route('status.callback', [
            'gateway' => 'marzpay',
            'trx' => $trxId,
        ]);
    }

    /**
     * Create a MarzPay mobile-money disbursement.
     */
    public function withdraw(
        $amount,
        $currency,
        $trxId,
        $withdrawCredential
    ) {
        $country = strtoupper(
            (string) ($this->credentials['country'] ?? 'UG')
        );

        $phone = $this->formatPhone(
            $withdrawCredential,
            $country
        );

        if (! $phone) {
            throw new Exception(
                "A valid {$country} mobile-money number is required for withdrawal."
            );
        }

        $callbackUrl = $this->callbackUrl();

        $reference = (string) Str::uuid();

        $payload = [
            'amount'       => (float) $amount,
            'phone_number' => $phone,
            'country'      => $country,
            'currency'     => strtoupper((string) $currency),
            'reference'    => $reference,
            'description'  => 'MarzPay wallet withdrawal',
            'callback_url' => $callbackUrl,
            'metadata'     => [
                [
                    'digikash_transaction' => $trxId,
                ],
            ],
        ];

        $response = $this->request(
            'post',
            '/send-money',
            $payload
        );

        Log::info('MARZPAY DISBURSEMENT DEBUG', [
            'http_status' => $response->status(),
            'api_status' => $response->json('status'),
            'message' => $response->json('message'),

            'transaction_status' => $response->json(
                'data.transaction.status'
            ),

            'provider' => $response->json(
                'data.disbursement.provider'
            ),

            'mode' => $response->json(
                'data.disbursement.mode'
            ),

            'provider_transaction_id' => $response->json(
                'data.disbursement.provider_transaction_id'
            ),

            'reference' => $reference,
            'local_transaction' => $trxId,

            'country' => $country,
            'currency' => strtoupper((string) $currency),

            'callback_host' => parse_url(
                $callbackUrl,
                PHP_URL_HOST
            ),
        ]);

        if (! $this->accepted($response)) {
            throw new Exception(
                'MarzPay withdrawal request failed: ' .
                (
                    $response->json('message')
                    ?? 'unknown provider error'
                )
            );
        }

        cache()->put(
            "marzpay_reference_{$reference}",
            $trxId,
            now()->addDay()
        );
    }

    /**
     * Handle MarzPay webhook/IPN.
     */
    public function handleIPN(Request $request): JsonResponse
    {
        /*
         * MarzPay webhook signing is optional.
         *
         * If webhook signing is disabled, unsigned webhooks
         * are accepted.
         */
        if (! $this->validSignature($request)) {
            return response()->json([
                'error' => 'Invalid webhook signature.',
            ], 401);
        }

        $payload = $request->json()->all();

        $reference = data_get(
            $payload,
            'transaction.reference'
        );

        $localTrxId =
            data_get(
                $payload,
                'metadata.0.digikash_transaction'
            )
            ??
            data_get(
                $payload,
                'metadata.0.reexpay_transaction'
            )
            ??
            (
                $reference
                    ? cache()->get(
                        "marzpay_reference_{$reference}"
                    )
                    : null
            );

        $status = strtolower(
            (string) data_get(
                $payload,
                'transaction.status'
            )
        );

        $event = strtolower(
            (string) data_get(
                $payload,
                'event_type'
            )
        );

        /*
         * Safe webhook debug information.
         */
        Log::info('MARZPAY WEBHOOK DEBUG', [
            'reference' => $reference,
            'local_transaction' => $localTrxId,
            'event' => $event,
            'status' => $status,

            'transaction_currency' => data_get(
                $payload,
                'transaction.amount.currency'
            ),

            'collection_amount' => data_get(
                $payload,
                'collection.amount.raw'
            ),

            'disbursement_amount' => data_get(
                $payload,
                'disbursement.amount.raw'
            ),

            'collection_provider' => data_get(
                $payload,
                'collection.provider'
            ),

            'collection_provider_transaction_id' => data_get(
                $payload,
                'collection.provider_transaction_id'
            ),

            'disbursement_provider_transaction_id' => data_get(
                $payload,
                'disbursement.provider_transaction_id'
            ),
        ]);

        if (! $localTrxId) {
            Log::warning(
                'MarzPay webhook could not map transaction.',
                [
                    'reference' => $reference,
                ]
            );

            return response()->json([
                'status' => 'ignored',
            ]);
        }

        $transaction = Transaction::findTransaction(
            $localTrxId
        );

        if (! $transaction) {
            Log::warning(
                'MarzPay webhook referenced an unknown transaction.',
                [
                    'trx_id' => $localTrxId,
                ]
            );

            return response()->json([
                'status' => 'ignored',
            ]);
        }

        if ($transaction->status?->value !== 'pending') {
            return response()->json([
                'status' => 'already_processed',
            ]);
        }

        $eventType = null;

        if (str_starts_with($event, 'collection.')) {
            $eventType = 'collection';
        } elseif (str_starts_with($event, 'disbursement.')) {
            $eventType = 'disbursement';
        }

        $expectedType = null;

        if ($transaction->trx_type === TrxType::DEPOSIT) {
            $expectedType = 'collection';
        } elseif ($transaction->trx_type === TrxType::WITHDRAW) {
            $expectedType = 'disbursement';
        }

        $providerAmount = data_get(
            $payload,
            $eventType === 'collection'
                ? 'collection.amount.raw'
                : 'disbursement.amount.raw'
        );

        $providerCurrency = data_get(
            $payload,
            'transaction.amount.currency'
        );

        /*
         * Prevent a webhook for another transaction from
         * settling the wrong local transaction.
         */
        if (
            $eventType !== $expectedType ||
            strtoupper((string) $providerCurrency)
                !== strtoupper((string) $transaction->currency) ||
            (float) $providerAmount
                !== (float) $transaction->payable_amount
        ) {
            Log::warning(
                'MarzPay webhook settlement mismatch.',
                [
                    'trx_id' => $localTrxId,
                    'event' => $event,

                    'provider_amount' => $providerAmount,
                    'expected_amount' => $transaction->payable_amount,

                    'provider_currency' => $providerCurrency,
                    'expected_currency' => $transaction->currency,
                ]
            );

            return response()->json([
                'status' => 'ignored',
            ], 422);
        }

        /*
         * Successful payment.
         */
        if (
            in_array(
                $status,
                ['completed', 'successful'],
                true
            )
            ||
            str_ends_with(
                $event,
                '.completed'
            )
        ) {
            Transaction::completeTransaction(
                $localTrxId
            );
        }

        /*
         * Failed payment.
         */
        elseif (
            in_array(
                $status,
                ['failed', 'cancelled'],
                true
            )
            ||
            str_ends_with(
                $event,
                '.failed'
            )
            ||
            str_ends_with(
                $event,
                '.cancelled'
            )
        ) {
            Transaction::failTransaction(
                $localTrxId
            );
        }

        if ($reference) {
            cache()->forget(
                "marzpay_reference_{$reference}"
            );
        }

        return response()->json([
            'status' => 'received',
        ]);
    }

    /**
     * Send authenticated request to MarzPay.
     */
    private function request(
        string $method,
        string $path,
        array $payload
    ) {
        return Http::withBasicAuth(
            $this->credentials['api_key'],
            $this->credentials['api_secret']
        )
            ->asForm()
            ->acceptJson()
            ->timeout(20)
            ->$method(
                self::BASE_URL . $path,
                $payload
            );
    }

    /**
     * Make sure MarzPay can reach our webhook.
     */
    private function callbackUrl(): string
    {
        $url = route(
            'ipn.handle',
            [
                'gateway' => 'marzpay',
            ]
        );

        $host = strtolower(
            (string) parse_url(
                $url,
                PHP_URL_HOST
            )
        );

        if (
            ! $host ||
            in_array(
                $host,
                [
                    'localhost',
                    '127.0.0.1',
                    '::1',
                ],
                true
            ) ||
            (
                filter_var(
                    $host,
                    FILTER_VALIDATE_IP
                ) &&
                ! filter_var(
                    $host,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE |
                    FILTER_FLAG_NO_RES_RANGE
                )
            )
        ) {
            throw new Exception(
                'MarzPay requires a public HTTPS callback URL. ' .
                'Set APP_URL to https://reexpaylimited.com before using live payments.'
            );
        }

        if (
            parse_url(
                $url,
                PHP_URL_SCHEME
            ) !== 'https'
        ) {
            throw new Exception(
                'MarzPay live payments require an HTTPS callback URL.'
            );
        }

        return $url;
    }

    /**
     * Determine whether MarzPay accepted the request.
     */
    private function accepted($response): bool
    {
        return $response->successful()
            && $response->json('status') === 'success'
            && is_array(
                $response->json(
                    'data.transaction'
                )
            );
    }

    /**
     * Validate MarzPay webhook signature.
     *
     * Webhook signing is optional.
     *
     * If no webhook secret exists, unsigned webhooks
     * are accepted.
     */
    private function validSignature(
        Request $request
    ): bool {
        $secret = trim(
            (string) (
                $this->credentials['webhook_secret']
                ?? ''
            )
        );

        /*
         * Webhook signing is disabled.
         *
         * Do NOT require signature headers when the
         * webhook secret is empty.
         */
        if ($secret === '') {
            Log::info(
                'MARZPAY WEBHOOK: signature verification disabled.'
            );

            return true;
        }

        /*
         * A secret exists, so MarzPay signing is enabled.
         * Signature headers are now required.
         */
        $timestamp = trim(
            (string) $request->header(
                'X-MarzPay-Timestamp',
                ''
            )
        );

        $signature = trim(
            (string) $request->header(
                'X-MarzPay-Signature',
                ''
            )
        );

        if (
            $timestamp === '' ||
            $signature === ''
        ) {
            Log::warning(
                'MarzPay webhook signature headers are missing while webhook signing is enabled.'
            );

            return false;
        }

        preg_match(
            '/(?:^|,)v1=([a-f0-9]+)(?:,|$)/i',
            $signature,
            $matches
        );

        if (! isset($matches[1])) {
            Log::warning(
                'MarzPay webhook signature format is invalid.'
            );

            return false;
        }

        if (
            ! ctype_digit($timestamp)
        ) {
            Log::warning(
                'MarzPay webhook timestamp is invalid.'
            );

            return false;
        }

        /*
         * Reject replayed or expired webhooks.
         */
        if (
            abs(
                time() - (int) $timestamp
            ) > 300
        ) {
            Log::warning(
                'MarzPay webhook timestamp is outside the allowed window.'
            );

            return false;
        }

        /*
         * MarzPay signature:
         *
         * HMAC-SHA256(
         *     timestamp + "." + raw_request_body,
         *     webhook_secret
         * )
         */
        $expected = hash_hmac(
            'sha256',
            $timestamp .
            '.' .
            $request->getContent(),
            $secret
        );

        if (
            ! hash_equals(
                $expected,
                $matches[1]
            )
        ) {
            Log::warning(
                'MarzPay webhook signature verification failed.'
            );

            return false;
        }

        return true;
    }

    /**
     * Normalize a mobile-money phone number.
     *
     * Accepted examples:
     *
     * 0771234567
     * 0771 234 567
     * 0771-234-567
     * 256771234567
     * +256771234567
     *
     * Returned format:
     *
     * +256771234567
     */
    private function formatPhone(
        ?string $phone,
        string $country
    ): ?string {
        if (! $phone) {
            return null;
        }

        /*
         * Remove spaces, brackets, hyphens and other
         * formatting while keeping a possible leading +.
         */
        $phone = preg_replace(
            '/[^0-9+]/',
            '',
            trim($phone)
        );

        if (! $phone) {
            return null;
        }

        $prefixes = [
            'UG' => '256',
            'KE' => '254',
            'RW' => '250',
            'CD' => '243',
        ];

        $prefix = $prefixes[$country] ?? null;

        if (! $prefix) {
            return null;
        }

        /*
         * Remove a leading + first.
         */
        if (str_starts_with($phone, '+')) {
            $phone = substr(
                $phone,
                1
            );
        }

        /*
         * Local format:
         *
         * 0771234567
         *
         * becomes:
         *
         * 256771234567
         */
        if (str_starts_with($phone, '0')) {
            $phone = $prefix .
                substr(
                    $phone,
                    1
                );
        }

        /*
         * Already international:
         *
         * 256771234567
         */
        elseif (
            str_starts_with(
                $phone,
                $prefix
            )
        ) {
            // Already correct country prefix.
        } else {
            return null;
        }

        /*
         * Final MarzPay format:
         *
         * +256 + 9 local digits
         */
        $formatted = '+' . $phone;

        $pattern = '/^\+' .
            preg_quote(
                $prefix,
                '/'
            ) .
            '[0-9]{9}$/';

        if (
            ! preg_match(
                $pattern,
                $formatted
            )
        ) {
            return null;
        }

        return $formatted;
    }
}