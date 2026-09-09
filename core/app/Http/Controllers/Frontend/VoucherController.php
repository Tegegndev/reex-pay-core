<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\CurrencyRole;
use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Wallet as WalletModel;
use App\Services\Handlers\VoucherHandler;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Transaction;
use Wallet;

class VoucherController extends Controller
{
    public function myVouchers()
    {
        $vouchers = Voucher::where('user_id', auth()->id())->latest()->get();

        return view('frontend.user.vouchers.index', compact('vouchers'));
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'amount'    => 'required|numeric',
        ]);

        // Extract validated data
        $amount   = $validated['amount'];
        $walletId = $validated['wallet_id'];

        // Fetch wallet and calculate fees
        $wallet     = WalletModel::findOrFail($walletId);
        $walletUuid = $wallet->uuid;

        $fee         = Wallet::calculateFeeByRole($wallet, $amount, CurrencyRole::VOUCHER);
        $totalAmount = $amount + $fee;

        // Convert amounts to wallet’s base currency
        $netAmount     = Wallet::conversionAmount($wallet, $amount);
        $payableAmount = Wallet::conversionAmount($wallet, $totalAmount);

        // Check if wallet balance is sufficient
        if (! Wallet::isWalletBalanceSufficient($walletUuid, $payableAmount)) {
            notifyEvs('error', __('Insufficient balance.'));

            return back();
        }

        // Validate amount limits
        Wallet::validateAmountLimitByRole($wallet, $amount, CurrencyRole::VOUCHER);

        // Perform the transaction within a database transaction
        DB::transaction(function () use ($walletUuid, $wallet, $amount, $fee, $netAmount, $payableAmount) {
            // Create transaction data
            $transactionData = new TransactionData(
                user_id: auth()->id(),
                trx_type: TrxType::VOUCHER,
                amount: $amount,
                amount_flow: AmountFlow::MINUS,
                fee: $fee,
                currency: siteCurrency(),
                net_amount: $netAmount,
                payable_amount: $payableAmount,
                payable_currency: $wallet->currency->code,
                wallet_reference: $walletUuid,
                description: __('Voucher issued from :wallet wallet', ['wallet' => $wallet->name]),
                status: TrxStatus::COMPLETED
            );

            // Create transaction record
            Transaction::create($transactionData);

            // Deduct money from the wallet
            Wallet::subtractMoney($wallet, $payableAmount);

            // Create a new voucher
            Voucher::create([
                'user_id'            => auth()->id(),
                'amount'             => $netAmount,
                'currency_id'        => $wallet->currency->id,
                'is_active'          => true,
                'redeemed_by'        => null,
                'redeemed_wallet_id' => null,
                'redeemed_at'        => null,
            ]);
        });

        notifyEvs('success', __('Voucher created successfully.'));

        return redirect()->back();
    }

    public function create()
    {
        $wallets = WalletModel::where('user_id', Auth::id())
            ->whereHas('currency.roles', fn ($query) => $query->where('role_name', CurrencyRole::VOUCHER)->where('is_active', true))
            ->with(['currency.roles' => fn ($query) => $query->where('role_name', CurrencyRole::VOUCHER)->where('is_active', true)])
            ->get();

        return view('frontend.user.vouchers.create', compact('wallets'));
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function redeem(Request $request, VoucherHandler $voucherHandler)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', $request->input('voucher_code'))->first();

        if (! $voucher) {
            throw new NotifyErrorException(__('Voucher not found.'));
        }

        if ($voucher->user_id === Auth::id()) {
            throw new NotifyErrorException(__('You are not allowed to redeem your own voucher.'));
        }

        if (! $voucher->isValid()) {
            throw new NotifyErrorException(__('Voucher is invalid or has already been redeemed.'));
        }

        $wallet = WalletModel::where('user_id', Auth::id())->where('currency_id', $voucher->currency_id)->first();

        if (! $wallet) {
            throw new NotifyErrorException('Wallet not found.');
        }

        DB::transaction(function () use ($voucherHandler, $voucher, $wallet) {
            $voucher->update([
                'is_active'          => false,
                'redeemed_by'        => $wallet->user_id,
                'redeemed_wallet_id' => $wallet->id,
                'redeemed_at'        => now(),
            ]);

            $transactionData = new TransactionData(
                user_id: auth()->id(),
                trx_type: TrxType::VOUCHER,
                amount: $voucher->amount,
                amount_flow: AmountFlow::PLUS,
                fee: 0,
                currency: $wallet->currency->code,
                net_amount: $voucher->amount,
                payable_amount: $voucher->amount,
                payable_currency: $wallet->currency->code,
                wallet_reference: $wallet->uuid,
                description: __('Voucher redeemed from :voucher Voucher Code', ['voucher' => $voucher->code]),
                status: TrxStatus::COMPLETED
            );

            $trx               = Transaction::create($transactionData);
            $trx->voucher_code = $voucher->code;

            $voucherHandler->handleSuccess($trx);
        });

        notifyEvs('success', __('Voucher redeemed successfully.'));

        return redirect()->back();
    }
}
