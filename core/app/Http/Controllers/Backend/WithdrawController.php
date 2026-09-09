<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use Illuminate\Http\Request;
use Transaction;

class WithdrawController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'manualRequest|history' => 'withdraw-list',
            'requestAction'         => 'withdraw-action',
        ];
    }

    public function manualRequest()
    {
        $withdrawRequests = Transaction::getTransactions(
            trx_type: TrxType::WITHDRAW,
            status: TrxStatus::PENDING,
            search: request('search'),
            dateRange: request('daterange'),
            processing_type: MethodType::MANUAL
        );

        return view('backend.withdraw.manual_request', compact('withdrawRequests'));
    }

    public function requestAction(Request $request)
    {
        $validated = $request->validate([
            'trx_id'  => 'required',
            'action'  => 'required|in:approve,reject',
            'remarks' => 'nullable|string',
        ]);

        if ($request->action === 'approve') {
            Transaction::completeTransaction($validated['trx_id'], $request->remarks);
        } else {
            Transaction::cancelTransaction($validated['trx_id'], $request->remarks, true);
        }

        notifyEvs('success', 'Withdraw request updated successfully');

        return redirect()->back();
    }

    public function history()
    {
        $withdrawHistories = Transaction::getTransactions(
            trx_type: TrxType::WITHDRAW,
            status: request('status'),
            search: request('search'),
            dateRange: request('daterange')
        );

        return view('backend.withdraw.history', compact('withdrawHistories'));
    }
}
