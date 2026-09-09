<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WithdrawAccount;
use App\Models\WithdrawMethod;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;

class WithdrawAccountController extends Controller
{
    use FileManageTrait;

    public function index()
    {
        if (request()->ajax()) {
            $walletId = request('wallet_id');

            // Validate and fetch the wallet with only necessary fields
            $withdrawWallet = Wallet::select('id', 'user_id', 'currency_id')
                ->where('id', $walletId)
                ->where('user_id', auth()->id())
                ->with([
                    'currency:id,code', // Load only necessary fields for the related model
                ])
                ->firstOrFail();

            // Fetch withdraw accounts with optimized query
            $withdrawAccounts = WithdrawAccount::select('id', 'user_id', 'name')
                ->with([
                    'withdrawMethod:id,currency', // Load only necessary fields
                ])
                ->where('user_id', auth()->id())
                ->whereHas('withdrawMethod', function ($query) use ($withdrawWallet) {
                    $query->where('currency', $withdrawWallet->currency->code)
                        ->where('status', true);
                })
                ->get();

            return response()->json($withdrawAccounts);
        }

        // Fetch withdraw accounts for the view
        $withdrawAccounts = WithdrawAccount::with('withdrawMethod')->where('user_id', auth()->id())->get();

        return view('frontend.user.withdraw.account.index', compact('withdrawAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'method_id'     => 'required|exists:withdraw_methods,id',
            'account_name'  => 'required',
            'credentials.*' => 'required',
        ]);

        $withdrawMethod = WithdrawMethod::active()
            ->whereKey($validated['method_id'])
            ->firstOrFail();

        $credentials = collect($withdrawMethod->fields)->map(function ($field) use ($validated) {
            if (isset($validated['credentials'][$field['name']]) && is_file($validated['credentials'][$field['name']])) {
                // Handle file upload
                $field['value'] = self::uploadImage($validated['credentials'][$field['name']]);
            } else {
                // Handle non-file inputs
                $field['value'] = $validated['credentials'][$field['name']] ?? null;
            }

            return $field;
        });

        WithdrawAccount::create([
            'user_id'            => auth()->user()->id,
            'withdraw_method_id' => $validated['method_id'],
            'name'               => $validated['account_name'],
            'credentials'        => $credentials,
        ]);

        notifyEvs('success', __('Withdraw Account Added Successfully'));

        return redirect()->route('user.withdraw.account.index');
    }

    public function create()
    {
        $withdrawMethods = WithdrawMethod::active()->get();

        return view('frontend.user.withdraw.account.create', compact('withdrawMethods'));
    }

    public function edit($id)
    {
        $withdrawAccount = WithdrawAccount::where('user_id', auth()->id())->findOrFail($id);

        return view('frontend.user.withdraw.account.edit', compact('withdrawAccount'));
    }

    public function update(Request $request, $id)
    {
        $withdrawAccount = WithdrawAccount::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'account_name'  => 'required',
            'credentials.*' => 'required',
        ]);

        $credentials = collect($withdrawAccount->credentials)->map(function ($field) use ($validated) {
            if (isset($validated['credentials'][$field['name']]) && is_file($validated['credentials'][$field['name']])) {
                // Handle file upload for updated files.
                $field['value'] = self::uploadImage($validated['credentials'][$field['name']], $field['value']);
            } elseif (isset($validated['credentials'][$field['name']])) {
                // Handle updated non-file inputs.
                $field['value'] = $validated['credentials'][$field['name']];
            }

            return $field;
        });

        // Update the withdrawal account details.
        $withdrawAccount->update([
            'name'        => $validated['account_name'],
            'credentials' => $credentials,
        ]);

        notifyEvs('success', __('Withdraw Account Updated Successfully'));

        return redirect()->route('user.withdraw.account.index');
    }

    public function accountInfo($id)
    {
        $withdrawAccount = WithdrawAccount::with(['withdrawMethod'])->findOrFail($id);

        $data = [
            'min_limit'       => $withdrawAccount->withdrawMethod->min_withdraw,
            'max_limit'       => $withdrawAccount->withdrawMethod->max_withdraw,
            'charge'          => $withdrawAccount->withdrawMethod->charge,
            'charge_type'     => $withdrawAccount->withdrawMethod->charge_type,
            'currency'        => $withdrawAccount->withdrawMethod->currency,
            'processing_time' => $withdrawAccount->withdrawMethod->processing_time,
            'conversion_rate' => $withdrawAccount->withdrawMethod->conversion_rate,
        ];

        return response()->json($data);
    }
}
