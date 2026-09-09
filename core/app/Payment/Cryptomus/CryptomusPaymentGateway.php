<?php

namespace App\Payment\Cryptomus;

use App\Facades\TransactionFacade as Transaction;
use App\Models\PaymentGateway;
use App\Payment\PaymentGateway as PaymentGatewayInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class CryptomusPaymentGateway implements PaymentGatewayInterface
{
    private const API_BASE = 'https://api.cryptomus.com/v1';

    private string $apiKey;

    private string $merchantId;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $credentials = PaymentGateway::getCredentials('cryptomus');

        if (empty($credentials['api_key']) || empty($credentials['merchant_id'])) {
            throw new Exception('Cryptomus API credentials are not set.');
        }

        $this->apiKey     = $credentials['api_key'];
        $this->merchantId = $credentials['merchant_id'];
    }

    /**
     * Initiate a Cryptomus payment and return the redirect URL.
     *
     * @param float  $amount
     * @param string $currency
     * @param string $txid
     *
     * @throws Exception
     */
    public function deposit($amount, $currency, $txid): string
    {
        $payload = [
            'amount'       => number_format($amount, 2, '.', ''), // e.g. "15.00"
            'currency'     => $currency,
            'order_id'     => $txid,
            'url_callback' => route('ipn.handle', ['gateway' => 'cryptomus']),
            'url_success'  => route('status.success', ['txid' => $txid]),
            'url_fail'     => route('status.cancel', ['txid' => $txid]),
            'is_test'      => 1, // test mode
        ];

        // 1) JSON-encode with unescaped Unicode
        $jsonBody = json_encode($payload, JSON_UNESCAPED_UNICODE);

        // 2) Base64 encode the JSON
        $base64 = base64_encode($jsonBody);

        // 3) MD5 hash: md5(base64(JSON) + apiKey)
        $signature = md5($base64.$this->apiKey);

        // 4) Send request with 'merchant' & 'sign' headers
        $response = Http::withHeaders([
            'merchant'     => $this->merchantId,
            'sign'         => $signature,
            'Content-Type' => 'application/json',
        ])->withBody($jsonBody, 'application/json')
            ->post(self::API_BASE.'/payment');

        if ($response->failed()) {
            throw new Exception('Cryptomus: cannot initiate payment.');
        }

        $data = $response->json('result', []);
        if (empty($data['url'])) {
            throw new Exception('Cryptomus: missing payment URL.');
        }

        return $data['url'];
    }

    /**
     * Handle Cryptomus webhook (IPN).
     */
    public function handleIPN(Request $request): Response
    {
        // 1) Read raw JSON and decode
        $content = $request->getContent();
        $data    = json_decode($content, true);

        // 2) Extract & remove 'sign'
        $sign = $data['sign'] ?? '';
        unset($data['sign']);

        // 3) Re-generate signature the same way:
        //    md5( base64_encode( json_encode(data) ) + apiKey )
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $check    = md5(base64_encode($jsonData).$this->apiKey);

        if (! hash_equals($check, $sign)) {
            return response('Invalid signature', 403);
        }

        // 4) Status handling
        $orderId = $data['order_id'] ?? null;
        $status  = $data['status']   ?? null;

        if ($orderId && $status === 'paid') {
            Transaction::completeTransaction($orderId);
        } else {
            Transaction::failTransaction($orderId);
        }

        return response('OK', 200);
    }
}
