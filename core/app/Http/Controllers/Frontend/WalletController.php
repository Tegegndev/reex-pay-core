<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet as WalletModel;
use Exception;
use Illuminate\Http\Request;
use Log;
use Wallet;

class WalletController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        app(\App\Services\WalletService::class)->getDefaultWallet($user);
        $wallets = $user->activeWallets();

        return view('frontend.user.wallet.index', compact('wallets'));
    }

    public function currencyInfo($currency_id)
    {
        $currency = Currency::with(['roles'])->findOrFail($currency_id);

        return view('frontend.user.wallet.partials._currency_info', compact('currency'))->render();
    }

    /**
     * @throws NotifyErrorException
     */
    public function create(Request $request)
    {
        // Validate the request input
        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
        ]);

        $user       = auth()->user(); // Get the authenticated user
        $currencyId = $validated['currency_id'];

        // Fetch user wallets count and rank limits
        $walletCount = $user->wallets()->count();
        $walletLimit = $user->rank->features['wallet_create'] ?? 0; // Default to 0 if undefined

        // Check if the wallet creation limit is reached
        if ($walletCount >= $walletLimit) {
            throw new NotifyErrorException(
                __('You have reached your wallet limit for :rankName Member', ['rankName' => $user->rank->name])
            );
        }

        // Create the wallet for the authenticated user
        $user->createWallet($currencyId);

        // Notify success and redirect back
        notifyEvs('success', __('Wallet created successfully'));

        return redirect()->back();
    }

    public function status(Request $request)
    {
        $wallet = auth()->user()->wallets()->findOrFail($request->id);

        $wallet->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('Wallet status updated successfully')]);
    }

    public function supportedPaymentMethods($wallet_id)
    {
        // Find the wallet belonging to the authenticated user
        $wallet = auth()->user()->wallets()->findOrFail($wallet_id);

        // Fetch supported payment methods based on the wallet's currency
        $paymentMethods = $wallet->supportedPaymentMethods($wallet->name)
            ->load('paymentGateway')
            ->makeHidden(['fields', 'receive_payment_details', 'status', 'created_at', 'updated_at'])
            ->toArray();

        // Return as JSON response
        return response()->json($paymentMethods);
    }

    public function validateRecipient($role, $emailOrWalletId)
    {
        $status         = 'error';
        $message        = __('Invalid input provided');
        $type           = null;
        $currencyRole   = null;
        $walletListView = null;

        if (filter_var($emailOrWalletId, FILTER_VALIDATE_EMAIL)) {
            // Handle email input
            $type = 'email';
            $user = User::where('email', $emailOrWalletId)->first();

            if (! $user) {
                $message = __('User not found');
            } elseif ($user->id === auth()->id()) {
                $message = __('You cannot perform this action for yourself');
            } else {
                $availableWallets = auth()->user()->activeWallets($role);
                $status           = 'success';
                $message          = __('User: :name', ['name' => "{$user->first_name} {$user->last_name}"]);
                $walletListView   = $this->renderWalletListView($availableWallets);
            }
        } elseif (ctype_digit($emailOrWalletId)) {
            // Handle wallet ID input
            $type   = 'wallet_uuid';
            $wallet = Wallet::getWalletByUniqueId((int) $emailOrWalletId);

            $availableMyWallet = auth()->user()->activeWallets()->where('currency_id', $wallet->currency_id);

            if (! $wallet) {
                $message = __('Wallet not found');
            } elseif (! $wallet->hasCurrencyRole($role)) {
                $message = __('This wallet does not permit :role', ['role' => title($role)]);
            } elseif ($availableMyWallet->isEmpty()) {
                $message = __('You do not have a wallet in this currency');
            } else {
                $user = User::find($wallet->user_id);

                if (! $user) {
                    $message = __('User associated with this wallet not found');
                } elseif ($user->id === auth()->id()) {
                    $message = __('You cannot perform this action for yourself');
                } else {
                    $status       = 'success';
                    $message      = __('User: :name', ['name' => "{$user->first_name} {$user->last_name}"]);
                    $currencyRole = $wallet->getCurrencyRoleInfo($role);

                    // Wrap the single wallet in an array for consistent handling in the view
                    $walletListView = $this->renderWalletListView($availableMyWallet);
                }
            }
        }

        // Return JSON response
        return response()->json([
            'type'              => $type ?? 'invalid',
            'available_wallets' => $walletListView,
            'currency_role'     => $currencyRole,
            'wallet_currency'   => isset($wallet) ? $wallet?->name : null,
            'currency_rate'     => isset($wallet) ? $wallet->currency->exchange_rate : null,
            'message'           => $message,
            'status'            => $status,
        ]);
    }

    /**
     * Returns wallet information for a given wallet ID.
     */
    public function getWalletInfo($role, $walletId)
    {
        try {
            $wallet = WalletModel::find($walletId);

            if (! $wallet) {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('Wallet not found'),
                ], 404);
            }

            // Safely retrieve wallet role and related currency details
            $walletRole = $wallet->getCurrencyRoleInfo($role) ?? [];
            $currency   = $wallet->currency;
            $walletInfo = array_merge($walletRole, [
                'wallet_currency' => $wallet->name,
                'currency_rate'   => $currency->exchange_rate,
            ]);

            return response()->json([
                'status' => 'success',
                'data'   => $walletInfo,
            ]);
        } catch (Exception $e) {
            // Log error for debugging
            Log::error($e);

            return response()->json([
                'status'  => 'error',
                'message' => __('An unexpected error occurred'),
            ], 500);
        }
    }

    /**
     * Render the wallet list view.
     */
    protected function renderWalletListView($wallets)
    {
        return view('frontend.user.wallet.partials._available_wallets', compact('wallets'))->render();
    }
}
