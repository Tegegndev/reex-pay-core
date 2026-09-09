<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * This function returns an array of middleware for the AuthController.
     *
     * The middleware ensures that only guests can access all routes except the 'logout' route.
     *
     * @return array The middleware array.
     */
    public static function middleware(): array
    {
        // Create a new instance of the Middleware class with the 'guest:admin' role
        // and the 'except' option set to ['logout'].
        return [
            new Middleware('guest:admin', except: ['logout']),
        ];
    }

    /**
     * @return Application|Factory|View
     */
    public function loginView()
    {
        return view('backend.auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @return RedirectResponse
     */
    public function authenticate(AdminLoginRequest $request)
    {
        // If reCAPTCHA is enabled, verify the token
        if ($request->isRecaptchaEnabled()) {
            $token = $request->input('g-recaptcha-response');
            if (! $request->verifyRecaptcha($token)) {
                notifyEvs('error', 'Failed to verify reCAPTCHA. Please try again. ❌');

                return back()->withInput($request->only('email'));
            }
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if ($this->guard()->attempt($credentials, $remember)) {
            notifyEvs('success', 'Welcome back! You are now logged in. 🚀');

            return redirect()->intended(route('admin.dashboard'));
        }

        notifyEvs('error', 'Invalid email or password. Please try again. ❌');

        return back()->withInput($request->only('email'));
    }

    /**
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        // Clear session completely
        $request->session()->invalidate();

        // Regenerate token to prevent session fixation
        $request->session()->regenerateToken();

        // Remove cached admin permission
        $request->session()->forget('admin_permissions');

        return redirect()->route('admin.login');
    }

    /**
     * @return Guard|StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
