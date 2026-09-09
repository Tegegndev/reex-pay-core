<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TrxType;
use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Transaction;
use Wallet;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::getTransactions(
            user_id: auth()->user()->id,
            trx_type: request('type'),
            status: request('status'),
            search: request('search'),
            dateRange: request('daterange')
        );

        return view('frontend.user.transaction.index', compact('transactions'));
    }

    /**
     * Handle transaction actions: save remarks, approve, or reject.
     *
     * @throws NotifyErrorException
     */
    public function handleAction(Request $request)
    {
        // Validate request inputs
        $validated = $request->validate([
            'trx_id'  => 'required|exists:transactions,trx_id',
            'remarks' => 'nullable|string|max:255',
            'action'  => 'required|in:approve,reject',
        ]);

        try {
            // Fetch the transaction
            $transaction = Transaction::findTransaction($validated['trx_id']);

            if (! $transaction) {
                throw new NotifyErrorException('Transaction not found.');
            }

            // Handle the approve action
            if ($validated['action'] === 'approve') {
                return $this->approveTransaction($transaction, $validated['remarks']);
            }

            // Handle the reject action
            if ($validated['action'] === 'reject') {
                return $this->rejectTransaction($transaction, $validated['remarks']);
            }
            throw new NotifyErrorException(__('Invalid action.'));
        } catch (Exception $e) {

            // Log the error and notify the user
            Log::error('Transaction handling error: '.$e->getMessage());

            throw new NotifyErrorException(__('An error occurred while processing the transaction.'));
        }
    }

    public function downloadPdf($trx_id)
    {
        // Retrieve the logo from the storage folder
        $logoPath = setting('logo'); // Assuming this returns a relative path like "logos/site-logo.png"

        $fileContent = Storage::get('public/'.$logoPath);
        $fileType    = pathinfo(Storage::path('public/'.$logoPath), PATHINFO_EXTENSION);
        $siteLogo    = 'data:image/'.$fileType.';base64,'.base64_encode($fileContent);

        // Retrieve transaction data
        $transaction = Transaction::findTransaction($trx_id);

        // Generate the PDF
        $pdf = Pdf::loadView('general.pdf.transaction', compact('transaction', 'siteLogo'));

        // Return the PDF for download
        return $pdf->download('transaction_receipt_'.$transaction->trx_id.'.pdf');
    }

    private function approveTransaction($transaction, $remarks)
    {
        if ($transaction->trx_type !== TrxType::REQUEST_MONEY && $transaction->status !== 'pending') {
            notifyEvs('error', 'Transaction cannot be approved.');

            return redirect()->back();
        }

        $payableAmount = $transaction->payable_amount;
        $myWalletUuid  = $transaction->wallet_reference;

        if (! Wallet::isWalletBalanceSufficient($myWalletUuid, $payableAmount)) {
            notifyEvs('error', 'Not enough balance in your wallet.');

            return redirect()->back();
        }

        // Complete transactions within a database transaction
        DB::transaction((function () use ($transaction, $remarks) {
            Transaction::completeTransaction($transaction->trx_id);
            Transaction::completeTransaction($transaction->trx_reference, $remarks);
        }));

        notifyEvs('success', 'Transaction approved successfully.');

        return redirect()->back();
    }

    private function rejectTransaction($transaction, $remarks)
    {

        // Cancel transactions within a database transaction
        DB::transaction((function () use ($transaction, $remarks) {
            Transaction::cancelTransaction($transaction->trx_id);
            Transaction::cancelTransaction($transaction->trx_reference, $remarks);
        }));

        notifyEvs('success', 'Transaction rejected successfully.');

        return redirect()->back();
    }
}
