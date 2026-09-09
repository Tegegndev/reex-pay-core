<?php

namespace App\Http\Requests\Auth;

use App\Traits\ReCaptchaValidation;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    use ReCaptchaValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        // Add reCAPTCHA rule if enabled in config
        return $this->addRecaptchaRuleIfConfigured($rules);
    }

    public function messages(): array
    {
        return array_merge([
            'login.required'    => 'Login field is required.',
            'password.required' => 'Password field is required.',
        ], $this->recaptchaValidationMessages());
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // ✅ Verify reCAPTCHA token if enabled
        if ($this->isRecaptchaEnabled()) {
            $token = $this->input('g-recaptcha-response');
            if (! $this->verifyRecaptcha($token)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Failed to verify reCAPTCHA. Please try again.',
                ]);
            }
        }

        $loginInput = $this->input('login');
        $loginField = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$loginField => $loginInput, 'password' => $this->input('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')).'|'.$this->ip());
    }
}
