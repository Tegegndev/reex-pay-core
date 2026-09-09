<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use App\Traits\FileManageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use FileManageTrait;

    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function profile()
    {

        $profileSections = [
            'profile_edit'    => 'profile-edit',
            'password_change' => 'security',
            '2fa_setup'       => 'qrcode',
        ];

        $admin = auth()->guard('admin')->user();

        if (! $admin->google2fa_secret) {
            $admin->google2fa_secret = $this->twoFactorService->generateSecret();
            $admin->save();
        }

        $qrCode = $this->twoFactorService->generateQrCode($admin->google2fa_secret, $admin->email);
        $secret = $admin->google2fa_secret;

        $activeSection = session('profile_section', 'profile_edit');

        return view('backend.profile.index', compact('admin', 'profileSections', 'qrCode', 'secret', 'activeSection'));
    }

    /**
     * Update the authenticated admins profile.
     *
     * @return RedirectResponse
     */
    public function updateInfo(Request $request)
    {

        session(['profile_section' => 'profile_edit']);

        // Retrieve the authenticated admin user
        $admin = auth()->guard('admin')->user(); // Assuming you have an 'admin' guard set up

        // Validate incoming data
        $validatedData = $request->validate([
            'profile' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cover'   => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:admins,email,'.$admin->id,
            'status'  => 'boolean',
        ]);

        // Handle file uploads (if provided)
        if ($request->hasFile('profile')) {
            $validatedData['profile'] = self::uploadImage($validatedData['profile'], $admin->profile);
        }

        if ($request->hasFile('cover')) {
            $validatedData['cover'] = self::uploadImage($validatedData['cover'], $admin->cover);
        }

        // Update the admin's record
        $admin->update($validatedData);

        // Send a success notification
        notifyEvs('success', __('Profile updated successfully!'));

        // Redirect back to the profile page (or wherever makes sense)
        return redirect()->back();
    }

    public function updatePassword(Request $request)
    {

        session(['profile_section' => 'password_change']);

        // Validate the request
        $request->validate([
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! Hash::check($value, auth('admin')->user()->password)) {
                        $fail(__('Old password does not match.'));
                    }
                },
            ],
            'new_password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);

        // Get the authenticated admin user
        $admin = auth('admin')->user();

        // Update the password
        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Notify success
        notifyEvs('success', __('Password updated successfully'));

        return redirect()->back();
    }

    /**
     * Enable 2FA for the user after verifying the code.
     *
     * @throws NotifyErrorException
     */
    public function enable2fa(Request $request)
    {
        session(['profile_section' => '2fa_setup']);

        $validate = $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $user = Auth::guard('admin')->user();
        $code = $validate['verification_code'];

        if ($this->twoFactorService->verifyCode($user->google2fa_secret, $code)) {
            $user->two_factor_enabled = true;
            $user->save();

            notifyEvs('success', 'Two-Factor Authentication enabled for user.');

            return redirect()->route('admin.profile.view');
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
        session(['profile_section' => '2fa_setup']);

        // Validate the user's password using Laravel "current_password" rule
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::guard('admin')->user();

        // Disable 2FA by clearing the secret and setting the flag to false.
        // Depending on your security needs, you may choose to keep the secret for re-enabling 2FA.
        $user->two_factor_enabled = false;
        $user->google2fa_secret   = null;
        $user->save();

        notifyEvs('success', __('Two-Factor Authentication has been disabled successfully.'));

        return redirect()->route('admin.profile.view');
    }
}
