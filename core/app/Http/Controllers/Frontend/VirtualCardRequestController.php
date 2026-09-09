<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\VirtualCard\CardholderStatus;
use App\Enums\VirtualCard\VirtualCardNetwork;
use App\Enums\VirtualCard\VirtualCardRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Cardholders;
use App\Models\VirtualCardProvider;
use App\Models\VirtualCardRequest;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class VirtualCardRequestController extends Controller
{
    /**
     * User dashboard: Show card requests, wallets, modal data.
     */
    public function index()
    {
        $user = Auth::user();

        // All wallets with currency info
        $wallets = $user->wallets()->with(['currency'])->get();

        // All virtual card requests for user
        $cardRequests = VirtualCardRequest::where('user_id', $user->id)
            ->with('wallet.currency')
            ->latest('id')
            ->get();

        // Fee info (min/max issue_fee from providers)
        $reqData = [
            'min_issue_fee' => \App\Models\VirtualCardProvider::min('issue_fee'),
            'max_issue_fee' => \App\Models\VirtualCardProvider::max('issue_fee'),
        ];

        // All supported networks (for modal select)
        $networks = VirtualCardNetwork::cases();

        $cardholders = Cardholders::where('user_id', $user->id)->where('status', CardholderStatus::APPROVED)->get();

        return view('frontend.user.virtual_card.request.index', compact('wallets', 'cardRequests', 'reqData', 'networks', 'cardholders'));
    }

    /**
     * Store a new virtual card request (from modal form).
     */
    public function store(Request $request)
    {
        $request->validate([
            'cardholder_id' => [
                'required',
                Rule::exists('cardholders', 'id')->where('status', CardholderStatus::APPROVED),
            ],
            'wallet_id' => [
                'required',
                Rule::exists('wallets', 'id')->where(fn ($q) => $q->where('user_id', auth()->id())),
            ],
            'network' => [
                'required',
                Rule::enum(VirtualCardNetwork::class),
            ],
        ]);

        $user = Auth::user();

        // Get selected wallet with currency
        $wallet = Wallet::where('id', $request->wallet_id)
            ->where('user_id', $user->id)
            ->with('currency')
            ->firstOrFail();

        // Check provider support for this network+currency
        $provider = VirtualCardProvider::active()
            ->whereJsonContains('supported_networks', $request->network)
            ->whereJsonContains('supported_currencies', $wallet->currency->code)
            ->first();

        if (! $provider) {
            notifyEvs('error', 'Selected network or currency is not supported by any provider.');

            return back();
        }

        // Create card request within a transaction
        \DB::beginTransaction();
        try {
            $cardRequest = VirtualCardRequest::create([
                'cardholder_id' => $request->cardholder_id,
                'wallet_id'     => $wallet->id,
                'user_id'       => $user->id,
                'network'       => $request->network,
                'status'        => VirtualCardRequestStatus::Pending,
            ]);

            // Notify admins with virtual card notification permission
            $admins = Admin::permission('virtual-card-notification')->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new \App\Notifications\TemplateNotification(
                    identifier: 'virtual_card_admin_notify_request',
                    data: [
                        'user'    => $user->full_name,
                        'network' => $request->network,
                        'wallet'  => $wallet->currency->code,
                    ],
                    sender: $user,
                    action: route('admin.virtual-card.requests.awaiting')
                ));
            }

            \DB::commit();
            notifyEvs('success', 'Your card request has been submitted for admin review.');
        } catch (\Exception $e) {
            \DB::rollBack();

            dd($e->getMessage());
            logger()->error('Failed to create virtual card request: '.$e->getMessage());
            notifyEvs('error', 'Failed to submit card request. Please try again.');

            return back();
        }

        return redirect()->route('user.virtual-card.request.index');
    }

    /**
     * (Optional) API endpoint for AJAX filtering eligible wallets (network+currency)
     */
    public function eligibleWallets(Request $request)
    {
        $network = $request->get('network');
        $user    = Auth::user();

        // Get all providers that support the selected network
        $providers = \App\Models\VirtualCardProvider::active()
            ->whereJsonContains('supported_networks', $network)
            ->get();

        // Collect all currencies supported by these providers
        $supportedCurrencies = $providers->flatMap(function ($provider) {
            return $provider->supported_currencies ?: [];
        })->unique()->values();

        // User wallets that have supported currency
        $wallets = $user->wallets()
            ->with('currency')
            ->whereHas('currency', function ($q) use ($supportedCurrencies) {
                $q->whereIn('code', $supportedCurrencies);
            })
            ->get();

        // Prepare for dropdown
        return response()->json($wallets->map(function ($wallet) {
            return [
                'id'   => $wallet->id,
                'text' => "{$wallet->currency->code} Wallet — {$wallet->currency->symbol}".number_format($wallet->balance, 2),            ];
        }));
    }
}
