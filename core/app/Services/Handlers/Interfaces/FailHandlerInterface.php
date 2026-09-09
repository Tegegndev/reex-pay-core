<?php

namespace App\Services\Handlers\Interfaces;

use App\Models\Transaction;

interface FailHandlerInterface
{
    public function handleFail(Transaction $transaction): void;
}
