<?php

namespace App\Observers;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Str;

class UserObserver
{
    public function created(User $user)
    {
        $user->update([
            'referral_code' => $this->generateReferralCode(),
        ]);
        app(WalletService::class)->createDefaultWalletForUser($user);
    }

    /**
     * Generate a unique referral code.
     */
    private function generateReferralCode(): string
    {
        do {
            $code = Str::random(8);
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
