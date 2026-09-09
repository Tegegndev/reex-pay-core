<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;

class IPNController extends Controller
{
    protected PaymentGatewayFactory $paymentFactory;

    public function __construct(PaymentGatewayFactory $paymentFactory)
    {
        $this->paymentFactory = $paymentFactory;
    }

    public function handleIPN(Request $request, $gateway)
    {

        // Create the correct gateway handler based on the URL parameter
        $paymentGateway = $this->paymentFactory->getGateway($gateway);

        // Handle IPN based on the specific gateway
        return $paymentGateway->handleIPN($request);
    }
}
