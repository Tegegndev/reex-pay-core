<?php

namespace App\Http\Requests\Auth;

use App\Traits\ReCaptchaValidation;
use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    use ReCaptchaValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];

        return $this->addRecaptchaRuleIfConfigured($rules);
    }

    public function messages(): array
    {
        return $this->recaptchaValidationMessages();
    }
}
