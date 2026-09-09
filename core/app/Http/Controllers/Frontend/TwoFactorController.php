<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show the 2FA setup form for users.
     */
    public function showSetupForm()
    {
        $user = Auth::user();

        if (! $user->google2fa_secret) {
            $user->google2fa_secret = $this->twoFactorService->generateSecret();
            $user->save();
        }

        $qrCode = $this->twoFactorService->generateQrCode($user->google2fa_secret, $user->email);
        $secret = $user->google2fa_secret;

        return view('frontend.user.setting.2fa_security', compact('qrCode', 'secret'));
    }

    /**
     * Enable 2FA for the user after verifying the code.
     *
     * @throws NotifyErrorException
     */
    public function enable2fa(Request $request)
    {
        $validate = $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $code = $validate['verification_code'];

        if ($this->twoFactorService->verifyCode($user->google2fa_secret, $code)) {
            $user->two_factor_enabled = true;
            $user->save();

            notifyEvs('success', 'Two-Factor Authentication enabled for user.');

            return redirect()->route('user.settings.2fa.setup');
        }

        throw new NotifyErrorException('Invalid verification code.');
    }

    /**
     * Disable Two-Factor Authentication.
     *
     * This method requires the user to confirm their password.
     *
     * @return RedirectResponse
     */
    public function disable2fa(Request $request)
    {
        // Validate the user's password using Laravel "current_password" rule
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Disable 2FA by clearing the secret and setting the flag to false.
        // Depending on your security needs, you may choose to keep the secret for re-enabling 2FA.
        $user->two_factor_enabled = false;
        $user->google2fa_secret   = null;
        $user->save();

        notifyEvs('success', __('Two-Factor Authentication has been disabled successfully.'));

        return redirect()->route('user.settings.2fa.setup');
    }
}
