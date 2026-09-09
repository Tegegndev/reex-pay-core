<?php

namespace App\Http\Controllers\Backend;

use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use Illuminate\Http\Request;
use Transaction;

class DepositController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'manualRequest|history' => 'deposit-list',
            'requestAction'         => 'deposit-action',

        ];
    }

    public function manualRequest()
    {
        $depositRequests = Transaction::getTransactions(
            trx_type: TrxType::DEPOSIT,
            status: TrxStatus::PENDING,
            search: request('search'),
            dateRange: request('daterange'),
            processing_type: MethodType::MANUAL
        );

        return view('backend.deposit.manual_request', compact('depositRequests'));
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

        notifyEvs('success', 'Deposit request updated successfully');

        return redirect()->back();

    }

    public function history()
    {

        $depositHistories = Transaction::getTransactions(
            trx_type: TrxType::DEPOSIT,
            status: request('status'),
            search: request('search'),
            dateRange: request('daterange')
        );

        return view('backend.deposit.history', compact('depositHistories'));
    }
}
