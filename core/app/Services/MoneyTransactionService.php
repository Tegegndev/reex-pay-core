<?php

namespace App\Services;

use App\Constants\CurrencyRole;
use App\Constants\FixPctType;
use App\Data\TransactionData;
use App\Enums\TrxStatus;
use App\Models\User;
use App\Models\Wallet as WalletModel;
use Transaction;
use Wallet;

class MoneyTransactionService
{
    /**
     * Retrieves a payer's wallet, given their identifier and currency ID.
     */
    protected function getRecipientWallet($recipient, $currencyId)
    {
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $recipientUser = User::where('email', $recipient)->first();

            return $recipientUser ? WalletModel::where('user_id', $recipientUser->id)->where('currency_id', $currencyId)->first() : null;
        }

        if (ctype_digit($recipient)) {
            return Wallet::getWalletByUniqueId((int) $recipient);
        }

        return null;
    }

    /**
     * Determines if the payer and requester wallets are the same.
     */
    protected function isSelfTransaction($recipientWallet, $requesterWallet)
    {
        return $recipientWallet->user_id === auth()->id() || $recipientWallet->id === $requesterWallet->id;
    }

    /**
     * Calculates the fee for requesting money, based on the requester's wallet and amount.
     */
    protected function calculateFee($wallet, $amount)
    {
        $currencyRole = $wallet->currency->roles()->where('role_name', CurrencyRole::REQUEST_MONEY)->first();

        return $currencyRole->fee_type === FixPctType::FIXED ? $currencyRole->fee : ($amount * $currencyRole->fee / 100);
    }

    /**
     * Calculates the amount of money the requester will actually receive, after deducting the fee.
     */
    protected function calculateRecipientAmount($wallet, $totalAmount, $fee)
    {
        $convertedFee         = $fee         * $wallet->currency->exchange_rate;
        $convertedTotalAmount = $totalAmount * $wallet->currency->exchange_rate;

        return $convertedTotalAmount - $convertedFee;
    }

    /**
     * Creates a new transaction for the request money functionality.
     */
    protected function createTransaction($userId, $type, $amount, $fee, $totalAmount, $netAmount, $wallet, $description = null, $note = null, $trxReference = null)
    {
        $data = new TransactionData(
            user_id: $userId,
            trx_type: $type,
            amount: $amount,
            fee: $fee,
            currency: siteCurrency(),
            net_amount: $netAmount,
            payable_amount: $totalAmount,
            payable_currency: $wallet->currency->code,
            wallet_reference: $wallet->uuid,
            trx_reference: $trxReference,
            remarks: $note,
            description: $description,
            status: TrxStatus::PENDING,
        );

        return Transaction::create($data);
    }
}
