<?php

namespace App\Services;

use App\Constants\FixPctType;
use App\Exceptions\NotifyErrorException;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Wallet as WalletModel;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletService
{
    /**
     * @throws NotifyErrorException
     */
    public function getDefaultWallet(User $user)
    {
        $defaultCurrency = Currency::getDefault();

        if (! $defaultCurrency) {
            throw new NotifyErrorException('Default currency not found.');
        }

        return $user->wallets()->where('currency_id', $defaultCurrency->id)->where('status', true)->first()
            ?? $this->createWallet($user, $defaultCurrency);
    }

    /**
     * Create a default wallet for a user if they don't have one with the default currency.
     */
    public function createDefaultWalletForUser(User $user): ?Wallet
    {
        // Fetch all auto wallet currencies
        $currencies = Currency::autoWallets();

        foreach ($currencies as $currency) {
            // Check if the user already has a wallet with this currency
            if (! $this->userHasWalletWithCurrency($user, $currency->id)) {
                $this->createWallet($user, $currency);
            }
        }

        return null; // Return null if no new wallet was created
    }

    /**
     * Create a wallet for a specified currency if the user doesn't already have one.
     */
    public function createWalletForCurrency(User $user, int $currencyId): ?Wallet
    {
        $currency = Currency::where('id', $currencyId)->where('status', true)->firstOrFail();

        return ! $this->userHasWalletWithCurrency($user, $currencyId) ? $this->createWallet($user, $currency) : null;
    }

    /**
     * @throws Exception
     */
    public function subtractMoneyByWalletUuid($walletUuid, $amount): WalletModel
    {
        try {
            $wallet = $this->getWalletByUniqueId($walletUuid);
        } catch (ModelNotFoundException $e) {
            throw new NotifyErrorException("Wallet with UUID {$walletUuid} not found.");
        }

        return $this->subtractMoney($wallet, $amount);
    }

    /**
     * Retrieve a wallet by its UniqueWalletId.
     *
     * @throws Exception
     */
    public function getWalletByUniqueId(string $uuid): Wallet
    {
        $wallet = Wallet::where('uuid', $uuid)->first();

        if (! $wallet) {
            throw new NotifyErrorException(__("Wallet with ID $uuid not found."));
        }

        return $wallet;
    }

    /**
     * Subtract money from a wallet.
     *
     * @throws Exception
     */
    public function subtractMoney(Wallet $wallet, float $amount): Wallet
    {
        if ($amount <= 0) {
            throw new NotifyErrorException('Amount must be greater than zero.');
        }

        $updated = Wallet::whereKey($wallet->id)
            ->where('status', true)
            ->where('balance', '>=', $amount)
            ->decrement('balance', $amount);

        if ($updated !== 1) {
            throw new NotifyErrorException('Insufficient balance in wallet.');
        }

        return $wallet->refresh();
    }

    /**
     * @throws Exception
     */
    public function addMoneyByWalletUuid($walletUuid, $amount): WalletModel
    {
        try {
            $wallet = $this->getWalletByUniqueId($walletUuid);
        } catch (ModelNotFoundException $e) {
            throw new NotifyErrorException("Wallet with UUID {$walletUuid} not found.");
        }

        return $this->addMoney($wallet, $amount);
    }

    /**
     * Add money to a wallet.
     *
     * @throws Exception
     */
    public function addMoney(Wallet $wallet, float $amount): Wallet
    {
        if ($amount <= 0) {
            throw new NotifyErrorException('Amount must be greater than zero.');
        }

        $wallet->increment('balance', $amount);

        return $wallet->refresh();
    }

    public function getWalletByUserId(int $userId, string $currencyCode): ?Wallet
    {
        return Wallet::where('user_id', $userId)
            ->whereHas('currency', function ($query) use ($currencyCode) {
                $query->where('code', $currencyCode);
            })
            ->first();
    }

    public function getDefaultWalletByUserId(int $userId): ?Wallet
    {
        $currency = Currency::getDefault();

        return self::getWalletByUserId($userId, $currency->code);
    }

    public function isWalletBalanceSufficient($walletUuid, $amount): bool
    {
        $myWallet = $this->getWalletByUniqueId($walletUuid);

        $walletBalance = $this->getWalletBalance($myWallet);

        return $walletBalance >= $amount;
    }

    /**
     * Get a wallet's balance.
     */
    public function getWalletBalance(Wallet $wallet): float
    {
        return $wallet->balance;
    }

    /**
     * Retrieves a payer's wallet, given their identifier and currency ID.
     *
     * @throws Exception
     */
    public function getWalletByUserEmailOrWalletUid($emailOrWalletUid, $currencyId): ?WalletModel
    {
        if (filter_var($emailOrWalletUid, FILTER_VALIDATE_EMAIL)) {
            $recipientUser = User::where('email', $emailOrWalletUid)->first();

            return $recipientUser ? WalletModel::where('user_id', $recipientUser->id)->where('currency_id', $currencyId)->first() : null;
        }

        if (ctype_digit($emailOrWalletUid)) {
            return self::getWalletByUniqueId((int) $emailOrWalletUid);
        }

        return null;
    }

    /**
     * Determines if the payer and requester wallets are the same.
     */
    public function isSelfTransaction($formWallet, $toWallet): bool
    {
        return $formWallet->user_id === auth()->id() || $formWallet->id === $toWallet->id;
    }

    /**
     * Calculates the fee for requesting money, based on the requester's wallet and amount.
     */
    public function calculateFeeByRole($wallet, $amount, $role)
    {

        $currencyRole = $wallet->currency->roles()->where('role_name', $role)->first();

        return $currencyRole->fee_type === FixPctType::FIXED ? $currencyRole->fee : ($amount * $currencyRole->fee / 100);
    }

    public function conversionAmount($wallet, $amount)
    {
        $rate = $wallet->currency->exchange_rate;

        return $amount * $rate;
    }

    /**
     * @throws Exception
     */
    public function validateAmountLimitByRole($requesterWallet, $payableAmount, $role): void
    {
        $currencyRole = $requesterWallet->currency->roles()->where('role_name', $role)->first();

        if ($payableAmount < $currencyRole->min_limit || $payableAmount > $currencyRole->max_limit) {
            $message = __('Amount must be between :min and :max', ['min' => $currencyRole->min_limit, 'max' => $currencyRole->max_limit]);
            throw new NotifyErrorException($message);
        }

    }

    /**
     * Check if a user already has a wallet in a specific currency.
     */
    protected function userHasWalletWithCurrency(User $user, int $currencyId): bool
    {
        return $user->wallets()->where('currency_id', $currencyId)->exists();
    }

    /**
     * Create a wallet for a user with a given currency.
     */
    protected function createWallet(User $user, Currency $currency): Wallet
    {
        return Wallet::create([
            'currency_id' => $currency->id,
            'user_id'     => $user->id,
            'uuid'        => $this->generateUniqueWalletId(),
            'balance'     => 0.0,
            'status'      => true,
        ]);
    }

    /**
     * Generate a unique wallet ID.
     */
    protected function generateUniqueWalletId(): string
    {
        do {
            $walletUuid = mt_rand(100000000, 999999999);
        } while (Wallet::where('uuid', $walletUuid)->exists());

        return (string) $walletUuid;
    }
}
