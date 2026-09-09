<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorVerificationController extends Controller
{
    protected TwoFactorService $twoFactorService;

    /**
     * Inject the TwoFactorService into the controller.
     */
    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function twoFactorChallenge()
    {
        return view('frontend.auth.two-factor-challenge');
    }

    public function twoFactorAuthenticate(Request $request)
    {
        // Validate the input: verification_code must be 6 digits
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Use the TwoFactorService to verify the code
        if ($this->twoFactorService->verifyCode($user->google2fa_secret, $request->input('verification_code'))) {
            // If the code is correct, set a session flag for 2FA verification
            $request->session()->put('2fa_verified', true);

            // Redirect to the intended user dashboard
            return redirect()->intended(route('user.dashboard'));
        }

        // If verification fails, redirect back with an error message
        return redirect()->back()->withErrors([
            'verification_code' => __('Invalid verification code.'),
        ]);
    }
}
