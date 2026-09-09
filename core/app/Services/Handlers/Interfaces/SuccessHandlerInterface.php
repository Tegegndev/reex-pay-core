<?php

namespace App\Services\Handlers\Interfaces;

use App\Models\Transaction;

interface SuccessHandlerInterface
{
    public function handleSuccess(Transaction $transaction): void;
}
