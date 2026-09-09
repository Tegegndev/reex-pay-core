<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Enums\VirtualCard\VirtualCardRequestStatus;
use App\Enums\VirtualCard\VirtualCardStatus;
use App\Models\VirtualCard;
use App\Models\VirtualCardProvider;
use App\Models\VirtualCardRequest;
use App\Notifications\TemplateNotification;
use App\VirtualCard\VirtualCardManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Transaction;
use Wallet;

class VirtualCardController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'virtualCardList|requestAwaiting|requestAll' => 'virtual-card-list',
            'review'                                     => 'virtual-card-action',
            'provider|providerManage|providerUpdate'     => 'virtual-card-provider-manage',
        ];
    }

    /**
     * List all virtual cards with admin filters.
     */
    public function virtualCardList(Request $request)
    {
        $cards = VirtualCard::with(['user', 'wallet.currency', 'provider'])
            ->filter($request)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
        $providers = VirtualCardProvider::all();
        $statuses  = VirtualCardStatus::options();

        return view('backend.virtual_card.list', compact('cards', 'providers', 'statuses'));
    }

    /**
     * List all pending virtual card requests with filters.
     */
    public function requestAwaiting(Request $request)
    {
        $requests = VirtualCardRequest::where('status', VirtualCardRequestStatus::Pending)
            ->with('wallet.currency', 'user')
            ->filter($request)
            ->latest('id')
            ->paginate(10);
        $providers = VirtualCardProvider::all();

        return view('backend.virtual_card.awaiting', compact('requests', 'providers'));
    }

    /**
     * List all virtual card requests with filters.
     */
    public function requestAll(Request $request)
    {
        $requests = VirtualCardRequest::with(['wallet.currency', 'user', 'card'])
            ->filter($request)
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
        $providers = VirtualCardProvider::all();
        $statuses  = VirtualCardRequestStatus::options();

        return view('backend.virtual_card.all', compact('requests', 'providers', 'statuses'));
    }

    /**
     * List all virtual card providers (paginated).
     */
    public function provider()
    {
        $providers = VirtualCardProvider::paginate(10);

        return view('backend.virtual_card.provider', compact('providers'));
    }

    /**
     * Show provider management form.
     */
    public function providerManage(int $id)
    {
        $provider = VirtualCardProvider::findOrFail($id);

        return view('backend.virtual_card.partials.provider_manage', compact('provider'));
    }

    /**
     * Update a virtual card provider.
     */
    public function providerUpdate(Request $request, VirtualCardProvider $provider)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:50',
            'issue_fee' => 'required|numeric|min:0',
            'status'    => 'boolean',
        ]);
        $validated['status'] = $request->boolean('status') ? 1 : 0;
        $provider->update($validated);
        notifyEvs('success', __('Provider Updated Successfully'));

        return redirect()->route('admin.virtual-card.provider.index');
    }

    /**
     * Review and process a virtual card request (approve/reject).
     */
    public function review(Request $request, string $uuid)
    {
        $cardRequest = VirtualCardRequest::where('uuid', $uuid)
            ->with('wallet.currency', 'user')
            ->firstOrFail();

        $request->validate([
            'provider_id' => ['required', 'string'],
            'admin_note'  => ['nullable', 'string', 'max:255'],
            'action'      => ['required', 'in:approve,reject'],
        ]);

        $cardRequest->provider_id = $request->provider_id;

        if ($request->action === 'approve') {
            DB::beginTransaction();
            try {
                $manager       = app(VirtualCardManager::class);
                $provider      = VirtualCardProvider::findOrFail($request->provider_id);
                $issueFee      = $provider->issue_fee;
                $user          = $cardRequest->user;
                $defaultWallet = $user->default_wallet;

                if (! $defaultWallet || $defaultWallet->balance < $issueFee) {
                    DB::rollBack();
                    notifyEvs('warning', 'Insufficient balance in your default wallet for card issuing fee.');

                    return back();
                }

                $trxData = new TransactionData(
                    user_id: $user->id,
                    trx_type: TrxType::SUBTRACT_BALANCE,
                    amount: $issueFee,
                    amount_flow: AmountFlow::MINUS,
                    currency: $defaultWallet->currency->code,
                    net_amount: $issueFee,
                    payable_amount: $issueFee,
                    payable_currency: $defaultWallet->currency->code,
                    wallet_reference: $defaultWallet->uuid,
                    description: __('Virtual Card Issuing Fee'),
                    status: TrxStatus::COMPLETED
                );
                $trx = Transaction::create($trxData);
                Wallet::subtractMoney($defaultWallet, $issueFee);
                $card = $manager->issueProviderCard($cardRequest, $provider->code);

                $cardRequest->update([
                    'status'     => VirtualCardRequestStatus::Issued,
                    'admin_note' => $request->admin_note,
                ]);

                $wallet      = $cardRequest->wallet;
                $walletName  = $wallet->name ?? $wallet->currency->code;
                $fee         = $issueFee;
                $cardNetwork = $card->brand ?? $cardRequest->network;
                $last4       = isset($card->number) ? substr($card->number, -4) : ($cardRequest->last4 ?? '');

                $cardRequest->user->notify(new TemplateNotification(
                    identifier: 'virtual_card_user_approved',
                    data: [
                        'card_network' => $cardNetwork,
                        'last4'        => $last4,
                        'wallet'       => $walletName,
                        'fee'          => $fee,
                    ],
                    action: route('user.virtual-card.index')
                ));

                DB::commit();
                notifyEvs('success', 'Virtual card has been issued successfully!');
            } catch (\Throwable $e) {
                DB::rollBack();
                logger()->error('Card issue failed: '.$e->getMessage(), ['exception' => $e]);
                notifyEvs('error', 'Card issuing failed: '.$e->getMessage());

                return back();
            }
        }

        if ($request->action === 'reject') {
            $cardRequest->update([
                'status'     => VirtualCardRequestStatus::Rejected,
                'admin_note' => $request->admin_note,
            ]);
            notifyEvs('warning', 'Request rejected successfully.');
        }

        return redirect()->route('admin.virtual-card.requests.awaiting');
    }
}
