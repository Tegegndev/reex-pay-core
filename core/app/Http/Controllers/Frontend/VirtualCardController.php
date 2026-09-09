<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\VirtualCard\CardholderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cardholders;
use App\Models\PaymentGateway;
use App\Models\VirtualCard;
use App\Models\VirtualCardFeeSetting;
use App\Models\VirtualCardProvider;
use App\VirtualCard\VirtualCardManager;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VirtualCardController extends Controller
{
    public function index()
    {
        $demoMode = config('app.demo') ?? false;

        $user    = Auth::user();
        $cards   = $user->virtualCards()->with('wallet.currency')->latest()->get();
        $wallets = Auth::user()->wallets()->with('currency')->get();

        $reqData = [
            'min_issue_fee'      => VirtualCardProvider::min('issue_fee'),
            'max_issue_fee'      => VirtualCardProvider::max('issue_fee'),
            'min_wallet_balance' => VirtualCardProvider::min('min_balance'),
            'max_wallet_balance' => VirtualCardProvider::max('min_balance'),
        ];

        // Prepare cache key exists info for each card
        $cardDetailAvailable = [];
        foreach ($cards as $card) {
            $cacheKey                       = 'virtual_card:'.auth()->id().":{$card->id}:full";
            $cardDetailAvailable[$card->id] = \Cache::has($cacheKey);
        }

        $cardholders = Cardholders::where('user_id', $user->id)->where('status', CardholderStatus::APPROVED)->get();

        return view('frontend.user.virtual_card.index', compact('cards', 'wallets', 'reqData', 'demoMode', 'cardDetailAvailable', 'cardholders'));
    }

    public function cardDetails($cardId, $providerName)
    {
        if ($providerName === 'strowallet') {

            $credentials = PaymentGateway::getCredentials('strowallet');

            try {
                $client   = new Client;
                $response = $client->request('POST', 'https://strowallet.com/api/bitvcard/fetch-card-detail/', [
                    'body' => json_encode([
                        'public_key' => $credentials['public_key'],
                        'mode'       => $credentials['mode'], // or 'live' as needed
                        'card_id'    => $cardId,
                    ]),
                    'headers' => [
                        'accept'       => 'application/json',
                        'content-type' => 'application/json',
                    ],
                    'timeout' => 10,
                ]);
                $data = json_decode($response->getBody(), true);

                if (isset($data['success']) && $data['success'] && isset($data['response']['card_detail'])) {
                    $cardDetails = $data['response']['card_detail'];

                    return view('frontend.user.virtual_card.partials._card_details', compact('cardDetails'))->render();
                } else {
                    $error = $data['message'] ?? 'Failed to retrieve card details.';

                    return response("<div class='alert alert-danger'>{$error}</div>");
                }
            } catch (\Exception $e) {
                return response("<div class='alert alert-danger'>API Error: {$e->getMessage()}</div>");
            }
        }

        // Handle other providers...
        return response("<div class='alert alert-warning'>Provider not supported.</div>");
    }

    public function topup($card)
    {
        $card         = VirtualCard::find($card);
        $cardSettings = VirtualCardFeeSetting::where('provider_id', $card->provider_id)->where('currency_id', $card->wallet->currency_id)->first();

        return view('frontend.user.virtual_card.topup.index', compact('card', 'cardSettings'));
    }

    public function withdraw($card)
    {
        $card         = VirtualCard::find($card);
        $cardSettings = VirtualCardFeeSetting::where('provider_id', $card->provider_id)->where('currency_id', $card->wallet->currency_id)->first();

        return view('frontend.user.virtual_card.withdraw.index', compact('card', 'cardSettings'));
    }

    public function topupStore(Request $request, VirtualCardManager $cardManager)
    {
        $validated = $request->validate([
            'card_id' => ['required', 'integer', 'exists:virtual_cards,id'],
            'amount'  => ['required', 'numeric', 'gt:0'],
        ]);

        \DB::beginTransaction();
        try {
            $cardManager->topup($validated['card_id'], $validated['amount']);
            \DB::commit();

            notifyEvs('success', __('Top-up successful.'));

            return redirect()->route('user.virtual-card.topup', ['card' => $validated['card_id']]);
        } catch (\App\Exceptions\NotifyErrorException $e) {
            \DB::rollBack();
            \Log::warning('Virtual Card Top-up Validation Error', [
                'user_id' => auth()->id(),
                'card_id' => $validated['card_id'],
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Virtual Card Top-up Error', [
                'user_id' => auth()->id(),
                'card_id' => $validated['card_id'],
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors(['amount' => __('Something went wrong. Please try again.')])->withInput();
        }
    }

    public function withdrawStore(Request $request, VirtualCardManager $cardManager)
    {
        $validated = $request->validate([
            'card_id' => ['required', 'integer', 'exists:virtual_cards,id'],
            'amount'  => ['required', 'numeric', 'gt:0'],
        ]);

        \DB::beginTransaction();
        try {

            $cardManager->withdraw($validated['card_id'], $validated['amount']);
            \DB::commit();

            notifyEvs('success', __('Withdraw successful.'));

            return redirect()->route('user.virtual-card.withdraw', ['card' => $validated['card_id']]);
        } catch (\App\Exceptions\NotifyErrorException $e) {
            \DB::rollBack();
            \Log::warning('Virtual Card Withdraw Validation Error', [
                'user_id' => auth()->id(),
                'card_id' => $validated['card_id'],
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {

            \DB::rollBack();
            \Log::error('Virtual Card Withdraw Error', [
                'user_id' => auth()->id(),
                'card_id' => $validated['card_id'],
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors(['amount' => __('Something went wrong. Please try again.')])->withInput();
        }
    }
}
