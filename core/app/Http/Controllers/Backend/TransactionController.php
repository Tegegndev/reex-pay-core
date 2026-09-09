<?php

namespace App\Http\Controllers\Backend;

use Transaction;

class TransactionController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index' => 'transaction-list',
        ];
    }

    public function index()
    {

        $transactions = Transaction::getTransactions(
            trx_type: request('type'),
            status: request('status'),
            search: request('search'),
            dateRange: request('daterange')
        );

        return view('backend.transaction.index', compact('transactions'));
    }
}
