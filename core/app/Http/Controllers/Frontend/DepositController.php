<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\MethodType;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\DepositMethod;
use App\Notifications\TemplateNotification;
use App\Services\Handlers\DepositHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Payment;

class DepositController extends Controller
{
    public function create()
    {
        $user            = auth()->user();
        $defaultWallet   = app(\App\Services\WalletService::class)->getDefaultWallet($user);
        $wallets         = $user->activeWallets();
        $depositMethods = DepositMethod::active()->get();

        return view('frontend.user.deposit.create', compact('wallets', 'depositMethods', 'defaultWallet'));
    }

    public function store(Request $request, DepositHandler $depositHandler)
    {
        $validatedData = $request->validate([
            'wallet_id'      => 'required|exists:wallets,id',
            'payment_method' => 'required|exists:deposit_methods,id',
            'amount'         => 'required|numeric|min:0.01',
        ]);

        try {

            // Get the payment gateway URL from the service
            [$transaction, $redirectUrl] = Payment::depositWithPaymentMethod(
                $validatedData['payment_method'],
                $validatedData['amount'],
                $validatedData['wallet_id']
            );

            // Notify the admin/user
            if ($transaction->processing_type === MethodType::MANUAL) {
                $depositHandler->handleSubmitted($transaction);
            }

            // Redirect the user to the payment gateway
            if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                // Redirect the user to the payment gateway for URL
                return redirect()->away($redirectUrl);
            } else {
                // Return the HTML view directly if $redirectUrl is HTML content
                return response($redirectUrl, 200)->header('Content-Type', 'text/html');

            }

        } catch (Exception $e) {
            // Redirect back with an error message
            notifyEvs('error', __('Deposit failed: :error', ['error' => $e->getMessage()]));

            return redirect()->back();
        }
    }

    public function credentials($method_id)
    {
        $method = DepositMethod::findOrFail($method_id);

        return view('frontend.user.deposit.partials._credentials_fields', compact('method'))->render();
    }

    public function history()
    {
        return view('frontend.user.deposit.history');
    }

    protected function depositNotification($transaction)
    {
        // Notify the user
        $transaction->user->notify(new TemplateNotification(
            identifier: 'deposit_user_submitted',
            data: [
                'amount' => $transaction->amount.' '.$transaction->currency,
                'method' => $transaction->provider,
                'trx'    => $transaction->trx_id,
            ],
            action: route('user.transaction.index')
        ));

        // Notify the admin
        $admins = Admin::permission('deposit-notification')->get();
        Notification::send($admins, new TemplateNotification(
            identifier: 'deposit_admin_notify_submission',
            data: [
                'user'   => $transaction->user->name,
                'amount' => $transaction->amount.' '.$transaction->currency,
                'method' => $transaction->provider,
                'trx'    => $transaction->trx_id,
            ],
            sender: $transaction->user,
            action: route('admin.deposit.manual-request')
        ));
    }
}
