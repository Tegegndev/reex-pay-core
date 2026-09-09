<?php

namespace App\Payment;

use App\Payment\Airtel\AirtelPaymentGateway;
use App\Payment\Binance\BinancePaymentGateway;
use App\Payment\Bitpayserver\BitpayserverPaymentGateway;
use App\Payment\Blockchain\BlockchainPaymentGateway;
use App\Payment\Blockio\BlockioPaymentGateway;
use App\Payment\Cashmaal\CashmaalPaymentGateway;
use App\Payment\Coinbase\CoinbasePaymentGateway;
use App\Payment\Coingate\CoingatePaymentGateway;
use App\Payment\Coinpayments\CoinpaymentsPaymentGateway;
use App\Payment\Cryptomus\CryptomusPaymentGateway;
use App\Payment\Flutterwave\FlutterwavePaymentGateway;
use App\Payment\Instamojo\InstamojoPaymentGateway;
use App\Payment\Mollie\MolliePaymentGateway;
use App\Payment\Moneroo\MonerooPaymentGateway;
use App\Payment\MTN\MTNPaymentGateway;
use App\Payment\MarzPay\MarzPayPaymentGateway;
use App\Payment\Nowpayments\NowpaymentsPaymentGateway;
use App\Payment\Paypal\PaypalPaymentGateway;
use App\Payment\Paystack\PaystackPaymentGateway;
use App\Payment\Razorpay\RazorpayPaymentGateway;
use App\Payment\Stripe\StripePaymentGateway;
use App\Payment\Twocheckout\TwocheckoutPaymentGateway;
use App\Payment\Voguepay\VoguepayPaymentGateway;
use Exception;
use Illuminate\Support\Facades\App;

class PaymentGatewayFactory
{
    /**
     * Create an instance of a payment gateway.
     *
     *
     * @throws Exception
     */
    public function getGateway(string $gatewayCode)
    {
        return match ($gatewayCode) {
            // Existing gateways
            'paypal'      => App::make(PaypalPaymentGateway::class),
            'stripe'      => App::make(StripePaymentGateway::class),
            'mollie'      => App::make(MolliePaymentGateway::class),
            'coinbase'    => App::make(CoinbasePaymentGateway::class),
            'paystack'    => App::make(PaystackPaymentGateway::class),
            'flutterwave' => App::make(FlutterwavePaymentGateway::class),
            'cryptomus'   => App::make(CryptomusPaymentGateway::class),
            'manual'      => App::make(ManualPaymentSystem::class),
            'moneroo'     => App::make(MonerooPaymentGateway::class),
            'voguepay'     => App::make(VoguepayPaymentGateway::class),
            'twocheckout'  => App::make(TwocheckoutPaymentGateway::class),
            'razorpay'     => App::make(RazorpayPaymentGateway::class),
            'nowpayments'  => App::make(NowpaymentsPaymentGateway::class),
            'mtn'          => App::make(MTNPaymentGateway::class),
            'instamojo'    => App::make(InstamojoPaymentGateway::class),
            'coinpayments' => App::make(CoinpaymentsPaymentGateway::class),
            'coingate'     => App::make(CoingatePaymentGateway::class),
            'cashmaal'     => App::make(CashmaalPaymentGateway::class),
            'bitpayserver' => App::make(BitpayserverPaymentGateway::class),
            'blockio'      => App::make(BlockioPaymentGateway::class),
            'blockchain'   => App::make(BlockchainPaymentGateway::class),
            'airtel'       => App::make(AirtelPaymentGateway::class),
            'marzpay'      => App::make(MarzPayPaymentGateway::class),
            'binance'      => App::make(BinancePaymentGateway::class),
            
            default       => throw new Exception(sprintf('Unsupported payment gateway: %s', $gatewayCode)),
        };
    }
}
