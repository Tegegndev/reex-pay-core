<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\Admin\ResetPasswordNotify;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    public function showForgetPasswordForm()
    {
        return view('backend.auth.forget_password');
    }

    public function submitForgetPasswordForm(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:admins',
        ]);

        try {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->insert([
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => Carbon::now(),
            ]);

            $admin = Admin::where('email', $request->email)->first();
            $admin->notify(new ResetPasswordNotify($token));

            notifyEvs('success', 'We have e-mailed your password reset link!');

            return back();

        } catch (Exception $e) {
            notifyEvs('error', $e->getMessage());

            return redirect()->back();
        }

    }

    public function showResetPasswordForm()
    {

        return view('backend.auth.reset_password');
    }

    public function submitResetPasswordForm(Request $request)
    {

        $request->validate([
            'email'                 => 'required|email|exists:admins',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        try {

            $updatePassword = DB::table('password_reset_tokens')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token,
                ])
                ->first();

            if (! $updatePassword) {
                notifyEvs('error', __('Invalid token!'));

                return redirect()->back();
            }

            Admin::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);

            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

            notifyEvs('success', 'Your password has been changed!');

            return redirect()->route('admin.login');

        } catch (Exception $e) {
            notifyEvs('error', 'Something went wrong, Please check error log');

            return redirect()->back();
        }

    }
}
