<?php

namespace App\Payment;

class ManualPaymentSystem
{
    public function deposit(): string
    {
        notifyEvs('success', __('Successful Requested and will be processed shortly.'));

        return route('user.transaction.index');
    }
}
