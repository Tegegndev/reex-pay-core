<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Facades\TransactionFacade as Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StatusController extends Controller
{
    /**
     * Handle a successful payment.
     */
    public function success(Request $request): RedirectResponse
    {
        $trxId = $request->input('trx_id');

        if ($trxId) {
            $transaction = Transaction::findTransaction($trxId);

            if ($transaction && $transaction->trx_type === TrxType::RECEIVE_PAYMENT) {
                $redirectUrl = $transaction->trx_data['success_redirect'] ?? null;

                if ($redirectUrl) {
                    return redirect()->away($redirectUrl);
                }
            }
        }

        notifyEvs('success', __('Successful Requested and will be processed shortly.'));
        return redirect()->route('user.transaction.index');
    }

    /**
     * Handle a payment cancellation.
     */
    public function cancel(Request $request): RedirectResponse
    {
        // Retrieve and remove the cancel transaction ID from session if not provided in the request.
        $trxId = $request->input('trx_id') ?: Session::pull('cancel_tnx');

        if (! $trxId) {
            notifyEvs('warning', __('Payment Canceled'));

            return redirect()->route('user.transaction.index');
        }

        $transaction = Transaction::findTransaction($trxId);

        // Only mark the transaction as failed if it exists.
        if ($transaction) {
            Transaction::failTransaction($trxId);
        }

        if ($transaction && $transaction->trx_type === TrxType::RECEIVE_PAYMENT) {
            $redirectUrl = $transaction->trx_data['cancel_redirect'] ?? null;

            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }
        }

        notifyEvs('warning', __('Payment Canceled'));

        return redirect()->route('user.transaction.index');
    }

    public function callback(Request $request)
    {
        $gateway = $request->input('gateway');
        $trxId = $request->input('trx');

        if (! $trxId || ! $gateway) {
            notifyEvs('error', __('Invalid callback parameters.'));
            return redirect()->route('user.transaction.index');
        }

        $transaction = \App\Facades\TransactionFacade::findTransaction($trxId);

        if (! $transaction) {
            notifyEvs('error', __('Transaction not found.'));
            return redirect()->route('user.transaction.index');
        }

        // Optionally verify status with gateway here, or trust webhook/IPN.
        if ($transaction->status === TrxStatus::COMPLETED) {
            notifyEvs('success', __('Payment Successful'));
        } elseif ($transaction->status === TrxStatus::FAILED) {
            notifyEvs('error', __('Payment Failed'));
        } else {
            notifyEvs('info', __('Payment is pending confirmation.'));
        }

        // Support merchant redirect if set
        if ($transaction->trx_type === \App\Enums\TrxType::RECEIVE_PAYMENT) {
            $redirectUrl = $transaction->trx_data['success_redirect'] ?? null;
            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }
        }

        return redirect()->route('user.transaction.index');
    }
}
