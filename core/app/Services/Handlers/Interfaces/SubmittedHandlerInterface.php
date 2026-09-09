<?php

namespace App\Services\Handlers\Interfaces;

use App\Models\Transaction;

interface SubmittedHandlerInterface
{
    public function handleSubmitted(Transaction $transaction): void;
}
