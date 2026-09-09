<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ReCaptchaValidation
{
    /**
     * Verify reCAPTCHA v2 token with Google's API.
     */
    public function verifyRecaptcha(string $token): bool
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $responseData = $response->json();

            return isset($responseData['success']) && $responseData['success'];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Add reCAPTCHA v2 validation rule if configured.
     */
    public function addRecaptchaRuleIfConfigured(array $rules): array
    {
        if ($this->isRecaptchaEnabled()) {
            $rules['g-recaptcha-response'] = ['required'];
        }

        return $rules;
    }

    /**
     * Check if reCAPTCHA is enabled.
     */
    public function isRecaptchaEnabled(): bool
    {
        return config('services.recaptcha.status', false) && config('services.recaptcha.secret');
    }

    /**
     * Provide the validation message for reCAPTCHA.
     */
    public function recaptchaValidationMessages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA.',
        ];
    }
}
