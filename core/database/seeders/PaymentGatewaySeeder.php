<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Seed the payment_gateways table with unique gateways.
     */
    public function run(): void
    {
        $gateways = [
            [
                'code'        => 'marzpay',
                'logo'        => null,
                'name'        => 'MarzPay Uganda Payments',
                'currencies'  => json_encode(['UGX']),
                'credentials' => json_encode([
                    'api_key'        => '',
                    'api_secret'     => '',
                    'webhook_secret' => '',
                    'country'        => 'UG',
                    'mode'           => 'sandbox',
                ]),
                'is_withdraw' => 'phone_number',
                'ipn'            => true,
                'status'         => false,
            ],
            [
                'code'        => 'moneroo',
                'logo'        => 'general/static/gateway/moneroo.svg',
                'name'        => 'Moneroo',
                'currencies'  => json_encode(['USD', 'EUR', 'GBP', 'BTC', 'ETH']),
                'credentials' => json_encode([
                    'api_key'                => 'api_key',
                    'api_secret'             => 'api_secret',
                    'webhook_signing_secret' => 'webhook_signing_secret',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => 1,
            ],
            [
                'code'        => 'strowallet',
                'logo'        => 'general/static/gateway/strowallet.png',
                'name'        => 'Strowallet',
                'currencies'  => json_encode(['USD', 'NGN']),
                'credentials' => json_encode([
                    'public_key' => 'public_key',
                    'secret_key' => 'secret_key',
                    'mode'       => 'sandbox',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'        => 'binance',
                'logo'        => 'general/static/gateway/binance.png',
                'name'        => 'Binance Pay',
                'currencies'  => json_encode(['USDT', 'BTC', 'ETH', 'BNB', 'BUSD', 'USD', 'EUR']),
                'credentials' => json_encode([
                    'certificate_sn' => 'certificate_sn',
                    'private_key' => 'private_key',
                    'sandbox' => false,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'airtel',
                'logo'           => 'general/static/gateway/airtel.png',
                'name'           => 'Airtel Money',
                'currencies'     => json_encode(['UGX', 'KES', 'TZS', 'RWF', 'ZMW']),
                'credentials'    => json_encode([
                    'client_id' => 'client_id',
                    'client_secret' => 'client_secret',
                    'country' => 'UG',
                    'currency' => 'UGX',
                    'sandbox' => true,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'blockchain',
                'logo'           => 'general/static/gateway/blockchain.png',
                'name'           => 'Blockchain.info',
                'currencies'     => json_encode(['BTC']),
                'credentials'    => json_encode([
                    'receive_address' => 'receive_address',
                    'callback_secret' => 'callback_secret',
                    'required_confirmations' => 1,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'blockio',
                'logo'           => 'general/static/gateway/blockio.png',
                'name'           => 'Block.io',
                'currencies'     => json_encode(['BTC', 'LTC', 'DOGE']),
                'credentials'    => json_encode([
                    'api_key' => 'api_key',
                    'required_confirmations' => 1,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'bitpayserver',
                'logo'           => 'general/static/gateway/btcpayserver.png',
                'name'           => 'BTCPay Server',
                'currencies'     => json_encode(['BTC', 'USD', 'EUR', 'GBP']),
                'credentials'    => json_encode([
                    'server_url' => 'https://your-btcpay-server.com',
                    'api_token' => 'api_token',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'cashmaal',
                'logo'           => 'general/static/gateway/cashmaal.png',
                'name'           => 'Cashmaal',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP', 'PKR']),
                'credentials'    => json_encode([
                    'web_id' => 'web_id',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'coingate',
                'logo'           => 'general/static/gateway/coingate.png',
                'name'           => 'CoinGate',
                'currencies'     => json_encode(['EUR', 'USD', 'BTC', 'ETH', 'LTC']),
                'credentials'    => json_encode([
                    'auth_token' => 'auth_token',
                    'receive_currency' => 'EUR',
                    'sandbox' => false,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'coinpayments',
                'logo'           => 'general/static/gateway/coinpayments.svg',
                'name'           => 'CoinPayments',
                'currencies'     => json_encode(['BTC', 'ETH', 'LTC', 'USDT', 'USD', 'EUR']),
                'credentials'    => json_encode([
                    'public_key' => 'public_key',
                    'private_key' => 'private_key', 
                    'ipn_secret' => 'ipn_secret',
                    'currency2' => 'BTC',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'instamojo',
                'logo'           => 'general/static/gateway/instamojo.png',
                'name'           => 'Instamojo',
                'currencies'     => json_encode(['INR']),
                'credentials'    => json_encode([
                    'api_key' => 'api_key', 
                    'auth_token' => 'auth_token',
                    'phone' => '9999999999',
                    'sandbox' => false,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'mtn',
                'logo'           => 'general/static/gateway/mtn.png',
                'name'           => 'MTN Mobile Money',
                'currencies'     => json_encode(['UGX', 'GHS', 'ZAR', 'XAF', 'EUR']),
                'credentials'    => json_encode([
                    'subscription_key' => 'subscription_key',
                    'user_id' => 'user_id',
                    'api_key' => 'api_key',
                    'test_msisdn' => '256774290781',
                    'sandbox' => true,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'nowpayments',
                'logo'           => 'general/static/gateway/nowpayments.png',
                'name'           => 'NOWPayments',
                'currencies'     => json_encode(['BTC', 'ETH', 'USDT', 'LTC', 'BCH', 'USD', 'EUR']),
                'credentials'    => json_encode([
                    'api_key' => 'api_key',
                    'ipn_secret' => 'ipn_secret',
                    'pay_currency' => 'BTC',
                    'sandbox' => false,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'razorpay',
                'logo'           => 'general/static/gateway/razorpay.png',
                'name'           => 'Razorpay',
                'currencies'     => json_encode(['INR', 'USD', 'EUR', 'GBP']),
                'credentials'    => json_encode([
                    'key_id' => 'key_id',
                    'key_secret' => 'key_secret',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'twocheckout',
                'logo'           => 'general/static/gateway/twocheckout.png',
                'name'           => '2Checkout',
                'currencies'     => json_encode(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
                'credentials'    => json_encode([
                    'merchant_code' => '...',
                    'secret_key' => '...',
                    'sandbox' => true,
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
            [
                'code'           => 'voguepay',
                'logo'           => 'general/static/gateway/voguepay.png',
                'name'           => 'Voguepay',
                'currencies'     => json_encode(['NGN', 'USD', 'GBP', 'EUR']),
                'credentials'    => json_encode([
                    'merchant_id' => 'merchant_id',
                ]),
                'is_withdraw' => null,
                'ipn'            => true,
                'status'         => true,
            ],
        ];

        foreach ($gateways as $gateway) {
            $attributes = ['code' => $gateway['code']];
            $values     = $gateway;
            unset($values['code']);
            DB::table('payment_gateways')->updateOrInsert($attributes, $values);
        }
    }
}
