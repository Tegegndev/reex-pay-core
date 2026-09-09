<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\CurrencyRole;
use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use App\Models\Wallet as WalletModel;
use App\Services\Handlers\SendMoneyHandler;
use DB;
use Throwable;
use Transaction;
use Wallet;

class SendMoneyController extends Controller
{
    public function create()
    {
        return view('frontend.user.send_money.create');
    }

    /**
     * Handle the submission of the send money form.
     */
    public function store(SendMoneyRequest $request, SendMoneyHandler $sendMoneyHandler)
    {
        // Extract validated data from the custom form request
        $recipient = $request->recipient;
        $amount    = $request->amount;
        $walletId  = $request->wallet_id;
        $note      = $request->note;

        try {
            // Fetch sender wallet
            $senderWallet = WalletModel::findOrFail($walletId);

            // Attempt to retrieve recipient’s wallet using a wallet helper function
            $recipientWallet = Wallet::getWalletByUserEmailOrWalletUid($recipient, $senderWallet->currency_id);

            if (! $recipientWallet) {
                notifyEvs('error', __('Recipient wallet not found or invalid input provided.'));

                return back();
            }

            // Check for self transaction
            if (Wallet::isSelfTransaction($recipientWallet, $senderWallet)) {
                notifyEvs('error', __('You cannot send money to yourself.'));

                return back();
            }

            // Calculate fees and amounts
            $fee         = Wallet::calculateFeeByRole($senderWallet, $amount, CurrencyRole::SENDER);
            $totalAmount = $amount + $fee;

            // Convert the total and fee to the wallet’s base currency to get net amounts
            $netAmount     = Wallet::conversionAmount($senderWallet, $amount);
            $payableAmount = Wallet::conversionAmount($senderWallet, $totalAmount);

            $isAmountSufficient = Wallet::isWalletBalanceSufficient($senderWallet->uuid, $payableAmount);

            if (! $isAmountSufficient) {
                notifyEvs('error', __('Insufficient balance.'));

                return back();
            }

            // valid amount Limit
            Wallet::validateAmountLimitByRole($senderWallet, $amount, CurrencyRole::SENDER);

            // Perform transaction
            DB::transaction(function () use (
                $sendMoneyHandler,
                $note,
                $payableAmount,
                $senderWallet,
                $recipientWallet,
                $amount,
                $fee,
                $netAmount
            ) {
                // Create sender transaction
                $senderData = new TransactionData(
                    user_id: $senderWallet->user_id,
                    trx_type: TrxType::SEND_MONEY,
                    amount: $amount,
                    amount_flow: AmountFlow::MINUS,
                    fee: $fee,
                    currency: siteCurrency(),
                    net_amount: $netAmount,
                    payable_amount: $payableAmount,
                    payable_currency: $senderWallet->currency->code,
                    wallet_reference: $senderWallet->uuid,
                    description: __('Sending money to :recipient', ['recipient' => $recipientWallet->user->name]),
                    status: TrxStatus::COMPLETED
                );
                $senderTrx = Transaction::create($senderData);

                // Create recipient transaction
                $recipientData = new TransactionData(
                    user_id: $recipientWallet->user_id,
                    trx_type: TrxType::RECEIVE_MONEY,
                    amount: $amount,
                    amount_flow: AmountFlow::PLUS,
                    fee: 0,
                    currency: siteCurrency(),
                    net_amount: $netAmount, // Recipient receives net amount after conversion
                    payable_amount: $netAmount,
                    payable_currency: $recipientWallet->currency->code,
                    wallet_reference: $recipientWallet->uuid,
                    trx_reference: $senderTrx->id,
                    remarks: $note,
                    description: __('Received money from :sender', ['sender' => $senderWallet->user->name]),
                    status: TrxStatus::COMPLETED
                );
                $recipientTrx = Transaction::create($recipientData);

                // Update transactions
                $senderTrx->update(['trx_reference' => $recipientTrx->id]);

                $senderTrx->ref_user_id = $recipientWallet->user_id;

                $recipientTrx->ref_user_id = $senderWallet->user_id;

                $sendMoneyHandler->handleSuccess($senderTrx);
                $sendMoneyHandler->handleSuccess($recipientTrx);
            });

            notifyEvs('success', __('Money sent successfully.'));

            return back();

        } catch (Throwable $e) {
            report($e);
            notifyEvs('error', __('An error occurred: :message', ['message' => $e->getMessage()]));

            return back();
        }
    }
}
