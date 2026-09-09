<?php

namespace App\Payment;

use Illuminate\Http\Request;

interface PaymentGateway
{
    public function deposit($amount, $currency, $trxId);

    public function handleIPN(Request $request);
}
