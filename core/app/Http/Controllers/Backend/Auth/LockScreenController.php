<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    public function show()
    {
        if (! session()->has('lock-screen')) {
            return redirect()->route('admin.dashboard');
        }

        return view('backend.auth.lock_screen');
    }

    public function lock()
    {

        session(['lock-screen' => true]);

        return redirect()->route('admin.lock-screen.show');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            session()->forget('lock-screen');

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Password is incorrect.']);
    }
}
