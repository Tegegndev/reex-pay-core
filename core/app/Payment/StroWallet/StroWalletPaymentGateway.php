<?php

namespace App\Payment\StroWallet;

use App\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StroWalletPaymentGateway implements PaymentGateway
{
    private $credentials;

    public function __construct()
    {
        $this->credentials = \App\Models\PaymentGateway::getCredentials('strowallet');

        if (empty($this->credentials['public_key']) || empty($this->credentials['secret_key']) || empty($this->credentials['webhook_signing_secret'])) {
            throw new \Exception('StroWallet API credentials are not configured.');
        }
    }

    public function deposit($amount, $currency, $trxId)
    {
        // Refactored to use StroWallet's GET request flow
        $parameters = [
            'amount'      => $amount,
            'currency'    => $currency,
            'details'     => 'Deposit for transaction '.$trxId,
            'custom'      => $trxId,
            'ipn_url'     => route('payment.ipn', ['gateway' => 'strowallet']),
            'success_url' => route('user.deposit.success'),
            'cancel_url'  => route('user.deposit.cancel'),
            'public_key'  => $this->credentials['public_key'],
            'name'        => 'John Doe',
            'email'       => 'customer@example.com',
        ];

        $endpoint = 'https://strowallet.com/express/initiate';
        $call     = $endpoint.'?'.http_build_query($parameters);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $call);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (isset($response_data['error']) && $response_data['error'] !== 'ok') {
            Log::error('StroWallet payment init error', ['response' => $response_data]);

            return back()->withErrors(['error' => 'StroWallet payment failed to initialize.']);
        }

        if (empty($response_data['url'])) {
            Log::error('StroWallet payment missing redirect URL', ['response' => $response_data]);

            return back()->withErrors(['error' => 'StroWallet redirect URL missing.']);
        }

        return redirect($response_data['url']);
    }

    public function handleIPN(Request $request)
    {
        $amount   = $request->input('amount');
        $currency = $request->input('currency');
        $custom   = $request->input('custom');
        $trx_num  = $request->input('trx_num');
        $sentSign = $request->input('signature');

        $string = $amount.$currency.$custom.$trx_num;
        $secret = $this->credentials['secret_key'];
        $mySign = strtoupper(hash_hmac('sha256', $string, $secret));

        if ($sentSign !== $mySign) {
            \Transaction::failTransaction($custom);
            abort(403, 'Invalid IPN signature');
        }

        \Transaction::completeTransaction($custom);

        return response()->json(['status' => 'success']);
    }
}
