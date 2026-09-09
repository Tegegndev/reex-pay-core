<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SettingController extends Controller
{
    use FileManageTrait;

    public function profile()
    {
        $user = auth()->user();

        return view('frontend.user.setting.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        // validation rules
        $validate = $request->validate([
            'avatar'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'first_name'       => 'nullable',
            'last_name'        => 'nullable',
            'business_name'    => 'nullable',
            'business_address' => 'nullable',
            'username'         => 'required|unique:users,username,'.$user->id,
            'gender'           => ['required', new Enum(Gender::class)],
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable',
            'country'          => 'nullable',
            'state'            => 'nullable',
            'city'             => 'nullable',
            'postal_code'      => 'nullable',
            'address'          => 'nullable',
            'email'            => 'required|unique:users,email,'.$user->id,
        ]);

        // if user uploaded a new avatar, update the avatar
        if ($request->hasFile('avatar')) {
            $validate['avatar'] = $this->uploadImage($request->file('avatar'), $user->avatar);
        }

        if ($user->email !== $validate['email']) {
            $validate['email_verified_at'] = null;
        }

        // update the user
        $user->update($validate);

        notifyEvs('success', 'Profile updated successfully');

        // return the user back to the form with a success message
        return redirect()->back();
    }

    public function verifyEmail()
    {

        if (auth()->user()->hasVerifiedEmail()) {
            notifyEvs('warning', 'Your email address is already verified');

            return redirect()->intended(route('user.settings.profile'));
        }

        auth()->user()->sendEmailVerificationNotification();
        notifyEvs('success', 'A fresh verification link has been sent to your email addres');

        // return the user back to the form with an error message
        return redirect()->back();
    }

    public function changePassword()
    {
        return view('frontend.user.setting.change_password');
    }

    public function passwordUpdate(Request $request)
    {
        $user     = auth()->user();
        $validate = $request->validate([
            'old_password' => 'required',
            'password'     => 'required|confirmed',
        ]);

        if (! password_verify($validate['old_password'], $user->password)) {
            notifyEvs('warning', 'Wrong current password');

            return redirect()->back();
        }
        $user->password = bcrypt($validate['password']);
        $user->save();
        notifyEvs('success', 'Password updated successfully');

        return redirect()->back();
    }
}
