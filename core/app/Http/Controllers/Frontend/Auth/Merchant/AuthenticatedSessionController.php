<?php

namespace App\Http\Controllers\Frontend\Auth\Merchant;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('frontend.auth.merchant.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Check if user has regular user role - if so, redirect to user login
        $user = Auth::user();
        if ($user->role !== UserRole::MERCHANT) {
            Auth::logout();
            notifyEvs('error', __('Please use regular user login for non-merchant accounts.'));

            return redirect()->route('user.login');
        }

        $request->session()->regenerate();

        app(WalletService::class)->createDefaultWalletForUser($user);

        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
