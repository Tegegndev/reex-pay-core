<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\CurrencyRole;
use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Models\Wallet as WalletModel;
use App\Services\CurrencyConversionService;
use App\Services\Handlers\ExchangeMoneyHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Transaction;
use Wallet;

class ExchangeMoneyController extends Controller
{
    /**
     * Handle the exchange money request.
     *
     * @throws Throwable
     */
    public function store(Request $request, CurrencyConversionService $currencyConversionService, ExchangeMoneyHandler $exchangeMoneyHandler)
    {
        $request->validate([
            'source_wallet_id'      => 'required|exists:wallets,uuid',
            'destination_wallet_id' => 'required|exists:wallets,uuid|different:source_wallet_id',
            'amount'                => 'required|numeric|min:0.01',
        ]);

        $sourceWallet      = Wallet::getWalletByUniqueId($request->source_wallet_id);
        $destinationWallet = Wallet::getWalletByUniqueId($request->destination_wallet_id);

        if (! $sourceWallet || ! $destinationWallet) {
            notifyEvs('error', 'Wallet not found');

            return redirect()->back();
        }

        $amount = $request->amount;

        // Calculate fees and total amount
        $fee         = Wallet::calculateFeeByRole($sourceWallet, $amount, CurrencyRole::EXCHANGE);
        $totalAmount = $amount + $fee;

        // Convert amounts to the wallet’s base currency
        $sourceNetAmount     = Wallet::conversionAmount($sourceWallet, $amount);
        $sourcePayableAmount = Wallet::conversionAmount($sourceWallet, $totalAmount);

        $destinationNetAmount = $currencyConversionService->convertCurrency($amount, $sourceWallet->currency->code, $destinationWallet->currency->code);

        // Check if the source wallet has sufficient balance
        $isBalanceSufficient = Wallet::isWalletBalanceSufficient($sourceWallet->uuid, $sourcePayableAmount);

        if (! $isBalanceSufficient) {
            notifyEvs('error', __('Insufficient balance.'));

            return back();
        }

        // Validate amount limit for the source wallet
        Wallet::validateAmountLimitByRole($sourceWallet, $amount, CurrencyRole::EXCHANGE);

        DB::transaction(function () use ($exchangeMoneyHandler, $sourceNetAmount, $fee, $amount, $destinationNetAmount, $sourcePayableAmount, $sourceWallet, $destinationWallet) {
            // Create source transaction
            $sourceTransactionData = new TransactionData(
                user_id: Auth::id(),
                trx_type: TrxType::EXCHANGE_MONEY,
                amount: $amount,
                amount_flow: AmountFlow::MINUS,
                fee: $fee,
                currency: siteCurrency(),
                net_amount: $sourceNetAmount,
                payable_amount: $sourcePayableAmount,
                payable_currency: $sourceWallet->currency->code,
                wallet_reference: $sourceWallet->uuid,
                description: __('Exchanging money to :wallet Wallet', ['wallet' => $sourceWallet->name]),
                status: TrxStatus::COMPLETED
            );
            $sourceTrx = Transaction::create($sourceTransactionData);

            // Create destination transaction by copying and overriding properties
            $destinationTransactionData = $sourceTransactionData->copy([
                'amount_flow'      => AmountFlow::PLUS,
                'fee'              => 0, // No fee for destination wallet in exchange
                'net_amount'       => $destinationNetAmount,
                'payable_amount'   => $destinationNetAmount,
                'payable_currency' => $destinationWallet->currency->code,
                'wallet_reference' => $destinationWallet->uuid,
                'trx_reference'    => $sourceTrx->trx_id,
                'description'      => __('Exchanging money from :wallet Wallet', ['wallet' => $destinationWallet->name]),
            ]);

            $destinationTrx = Transaction::create($destinationTransactionData);

            $destinationTrx->from_currency = $sourceWallet->currency->code;
            $destinationTrx->from_amount   = $sourcePayableAmount;

            // Trigger notifications & balance sync
            $exchangeMoneyHandler->handleSuccess($sourceTrx);
            $exchangeMoneyHandler->handleSuccess($destinationTrx);
        });
        notifyEvs('success', __('Money exchanged successfully.'));

        return redirect()->route('user.transaction.index');
    }

    /**
     * Show the exchange money form.
     */
    public function create()
    {
        $userWallets = WalletModel::where('user_id', Auth::id())
            ->whereHas('currency.roles', fn ($query) => $query->where('role_name', CurrencyRole::EXCHANGE)->where('is_active', true))
            ->with(['currency.roles' => fn ($query) => $query->where('role_name', CurrencyRole::EXCHANGE)->where('is_active', true)])
            ->get();

        return view('frontend.user.exchange_money.create', compact('userWallets'));
    }
}
