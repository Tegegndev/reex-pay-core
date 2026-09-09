<?php

namespace App\Http\Controllers\Frontend\Auth\User;

use App\Enums\UserRole;
use App\Events\TransactionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserFeature;
use Cookie;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('frontend.auth.user.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'country'    => 'required|string',
            'phone'      => 'required|string',
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Parse country data
        [$countryName, $countryCode] = explode(':', $validated['country']);
        $formattedPhone              = formatPhone($countryCode, $validated['phone']);

        // 3. Determine location (fallback if session missing)
        $location = session('user_location') ?? getLocation();

        // 4. Create user (always as regular user)
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'username'   => $validated['username'],
            'email'      => $validated['email'],
            'country'    => $countryName ?? $location['name'],
            'phone'      => $formattedPhone,
            'role'       => UserRole::USER,
            'password'   => Hash::make($validated['password']),
        ]);

        // 5. Handle referral
        if ($referralCode = Cookie::get('referral_code')) {
            $referrer = User::where('referral_code', $referralCode)->first();

            if ($referrer) {
                $parentReferral = Referral::where('referred_user_id', $referrer->id)->first();

                Referral::create([
                    'user_id'            => $referrer->id,
                    'referred_user_id'   => $user->id,
                    'parent_referral_id' => optional($parentReferral)->id,
                ]);
            }
        }

        // 6. Trigger events and login
        event(new Registered($user));
        event(new TransactionUpdated($user));
        UserFeature::syncWithConfigForUser($user->id);
        Auth::login($user);

        notifyEvs('success', __('Registration successful.'));

        return redirect()->route('user.dashboard');
    }
}
