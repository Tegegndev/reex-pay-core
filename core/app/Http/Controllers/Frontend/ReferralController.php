<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralContent;
use Illuminate\Support\Facades\Auth;
use Transaction;

class ReferralController extends Controller
{
    public function index()
    {
        // Get the root referrals for the logged-in user
        $referrals = Referral::with('childReferrals.referredUser')
            ->where('user_id', Auth::id())
            ->get();

        $referralsRewards = Transaction::getTransactions(
            user_id: auth()->user()->id,
            trx_type: TrxType::REFERRAL_REWARD
        );

        // Get referral content (always exists)
        $referralContent = ReferralContent::getContent();

        // Pass the root referrals to the view
        return view('frontend.user.referral.index', compact('referrals', 'referralsRewards', 'referralContent'));
    }
}
