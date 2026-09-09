<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardholderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow if user is authenticated
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'card_type' => ['required', 'string'],
            'country_b' => 'nullable|string|max:10',
            // Business fields
            'business_name'       => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'tin'                 => 'nullable|string|max:100',
            'business_type'       => 'nullable|string|max:100',
            'contact_email'       => 'nullable|email|max:100',
            'contact_phone'       => 'nullable|string|max:30',
            'address_line1_b'     => 'nullable|string|max:255',
            'address_line2_b'     => 'nullable|string|max:255',
            'city_b'              => 'nullable|string|max:100',
            'state_b'             => 'nullable|string|max:100',
            'postal_code_b'       => 'nullable|string|max:20',
            // Personal fields
            'first_name'      => 'nullable|string|max:191',
            'last_name'       => 'nullable|string|max:191',
            'email'           => 'nullable|email|max:191',
            'mobile'          => 'nullable|string|max:30',
            'gender'          => ['nullable', 'string'],
            'dob'             => 'nullable|date',
            'relation'        => 'nullable|string|max:100',
            'address_line1'   => 'nullable|string|max:191',
            'address_line2'   => 'nullable|string|max:191',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:10',
            'kyc_template_id' => 'nullable|exists:kyc_templates,id',
            'credentials'     => 'nullable|array',
        ];
        if ($this->input('card_type') === \App\Enums\VirtualCard\CardholderType::PERSONAL->value) {
            $rules['first_name']      = ['required', 'string', 'max:191'];
            $rules['last_name']       = ['required', 'string', 'max:191'];
            $rules['kyc_template_id'] = ['required', 'exists:kyc_templates,id'];
            $rules['credentials']     = ['required', 'array'];
        } elseif ($this->input('card_type') === \App\Enums\VirtualCard\CardholderType::BUSINESS->value) {
            $rules['business_name']   = ['required', 'string', 'max:255'];
            $rules['business_type']   = ['required', 'string', 'max:100'];
            $rules['contact_email']   = ['required', 'email', 'max:100'];
            $rules['contact_phone']   = ['required', 'string', 'max:30'];
            $rules['address_line1_b'] = ['required', 'string', 'max:255'];
            $rules['country_b']       = ['required', 'string', 'max:10'];
        }

        return $rules;
    }
}
