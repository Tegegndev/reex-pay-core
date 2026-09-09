<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WithdrawAccount;
use App\Models\WithdrawMethod;
use App\Models\WithdrawSchedule;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Payment;

class WithdrawController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $defaultWallet = app(WalletService::class)->getDefaultWallet($user);
        $isWithdrawEnabledToday = WithdrawSchedule::isWithdrawEnabledToday();

        $withdrawOffDays = WithdrawSchedule::where('status', false)->pluck('day')->toArray();

        $wallets = $user->activeWallets()->filter(fn ($wallet) => $wallet->balance > 0);

        return view('frontend.user.withdraw.create', compact('wallets', 'defaultWallet', 'isWithdrawEnabledToday', 'withdrawOffDays'));
    }

    /**
     * @throws Exception
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:withdraw_accounts,id',
            'wallet_id'  => 'required|exists:wallets,id',
            'amount'     => 'required|numeric|min:1',
        ]);

        if (! WithdrawSchedule::isWithdrawEnabledToday()) {
            throw new NotifyErrorException('Withdrawals are not enabled for today.');
        }

        $account = WithdrawAccount::with(['withdrawMethod'])
            ->where('user_id', auth()->id())
            ->findOrFail($validated['account_id']);
        $wallet = auth()->user()->wallets()
            ->whereKey($validated['wallet_id'])
            ->where('status', true)
            ->whereHas('currency', fn ($query) => $query->where('code', 'UGX'))
            ->firstOrFail();

        $amount = $validated['amount'];

        if (! $account->withdrawMethod->isWithinLimits($amount)) {
            throw new NotifyErrorException('Amount is not within the allowed limits.');
        }

        Payment::withdrawMoney($account, $wallet, $amount);

        notifyEvs('success', __('Withdrawal Requested and will be processed shortly.'));

        return redirect()->route('user.transaction.index');

    }

    public function credentialsFields($method_id)
    {

        $method = WithdrawMethod::find($method_id);

        $view = view('frontend.user.withdraw.partials.credentials_fields', compact('method'));

        return response()->json(['html' => $view->render(), 'method_name' => $method->name]);

    }
}
