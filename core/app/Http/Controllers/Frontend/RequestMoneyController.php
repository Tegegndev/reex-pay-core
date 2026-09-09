<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\CurrencyRole;
use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Models\Wallet as WalletModel;
use App\Notifications\TemplateNotification;
use App\Services\Handlers\RequestMoneyHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Transaction;
use Wallet;

class RequestMoneyController extends Controller
{
    /**
     * Displays the form for requesting money from another user.
     */
    public function create()
    {
        return view('frontend.user.request_money.create');
    }

    /**
     * Processes the form for requesting money from another user.
     */
    public function store(Request $request, RequestMoneyHandler $requestMoneyHandler)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'recipient' => ['required'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'note'      => ['nullable', 'max:255'],
        ]);

        try {
            // Extract validated data
            $recipient = $validated['recipient'];
            $amount    = $validated['amount'];
            $note      = $request->note;

            // Retrieve requester and recipient wallets
            $requesterWallet = WalletModel::findOrFail($validated['wallet_id']);
            $recipientWallet = wallet::getWalletByUserEmailOrWalletUid($recipient, $requesterWallet->currency_id);

            // Check recipient wallet validity
            if (! $recipientWallet) {
                notifyEvs('error', __('Payer wallet not found or invalid input provided.'));

                return redirect()->back();
            }

            // Prevent self transactions
            if (wallet::isSelfTransaction($recipientWallet, $requesterWallet)) {
                notifyEvs('error', __('You cannot request money from yourself.'));

                return redirect()->back();
            }

            // Calculate fees and amounts
            $fee         = wallet::calculateFeeByRole($requesterWallet, $amount, CurrencyRole::REQUEST_MONEY);
            $totalAmount = $amount + $fee;
            $netAmount   = wallet::conversionAmount($requesterWallet, $amount);

            $payableAmount = wallet::conversionAmount($requesterWallet, $totalAmount);

            // Validate amount limit
            Wallet::validateAmountLimitByRole($requesterWallet, $amount, CurrencyRole::REQUEST_MONEY);

            // Create transactions
            DB::transaction(function () use ($requestMoneyHandler, $payableAmount, $note, $requesterWallet, $recipientWallet, $amount, $fee, $netAmount) {

                $requesterData = new TransactionData(
                    user_id: auth()->id(),
                    trx_type: TrxType::REQUEST_MONEY,
                    amount: $amount,
                    amount_flow: AmountFlow::PLUS,
                    fee: $fee,
                    currency: siteCurrency(),
                    net_amount: $netAmount,
                    payable_amount: $payableAmount,
                    payable_currency: $requesterWallet->currency->code,
                    wallet_reference: $requesterWallet->uuid,
                    description: __('You have requested money from :recipient.', ['recipient' => $recipientWallet->user->name]),
                    status: TrxStatus::PENDING,
                );

                $requesterTransaction = Transaction::create($requesterData);

                // Create the recipient transaction data by copying and overriding properties
                $recipientData = $requesterData->copy([
                    'user_id'          => $recipientWallet->user_id,
                    'trx_type'         => TrxType::REQUEST_MONEY,
                    'amount_flow'      => AmountFlow::MINUS,
                    'fee'              => 0, // No fee for recipient
                    'wallet_reference' => $recipientWallet->uuid,
                    'description'      => __(':requester has requested money from you.', ['requester' => $requesterWallet->user->name]),
                    'trx_reference'    => $requesterTransaction->trx_id,
                    'remarks'          => $note,
                ]);

                $recipientTransaction = Transaction::create($recipientData);

                $requesterTransaction->update(['trx_reference' => $recipientTransaction->trx_id]);

                $requestMoneyHandler->handleSubmitted($requesterTransaction);

            });

            notifyEvs('success', __('Money request sent successfully.'));

            return redirect()->back();

        } catch (Throwable $e) {
            report($e);
            notifyEvs('error', __('An error occurred: :message', ['message' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    private function notifications($requester, $receiver, $transaction)
    {
        $requester->notify(new TemplateNotification(
            identifier: 'request_money_user_requested',
            data: [
                'amount'    => $transaction->amount.' '.$transaction->currency,
                'recipient' => $receiver->name,
                'trx'       => $transaction->trx_id,
            ],
            action: route('user.transaction.index')

        ));
        $receiver->notify(new TemplateNotification(
            identifier: 'request_money_user_received',
            data: [
                'amount' => $transaction->amount.' '.$transaction->currency,
                'sender' => $requester->name,
                'trx'    => $transaction->trx_id,
            ],
            action: route('user.transaction.index')
        ));
    }
}
