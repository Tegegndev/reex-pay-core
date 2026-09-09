<?php

namespace App\Http\Requests\Frontend;

use App\Enums\KycStatus;
use App\Enums\VirtualCard\CardholderType;
use App\Models\Cardholders;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCardholderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Add your authorization logic if needed
        return true;
    }

    public function rules(): array
    {
        /** @var Cardholders $cardholder */
        $cardholder = $this->route('cardholder') ?? $this->route('id');
        if (is_numeric($cardholder)) {
            $cardholder = Cardholders::findOrFail($cardholder);
        }

        $rules = [
            'card_type' => [
                'required',
                Rule::in(array_keys(CardholderType::options())),
                function ($attribute, $value, $fail) use ($cardholder) {
                    if ($cardholder && $value !== $cardholder->card_type->value) {
                        $fail(__('Cardholder type cannot be changed.'));
                    }
                },
            ],
            // KYC template id is required only if KYC status is REJECTED
            'kyc_template_id' => [
                Rule::requiredIf($cardholder && $cardholder->kyc_status === KycStatus::REJECTED),
                'nullable',
                'exists:kyc_templates,id',
            ],
            // Add all other fields as per your create rules
            'first_name'    => ['nullable', 'string', 'max:100'],
            'last_name'     => ['nullable', 'string', 'max:100'],
            'email'         => ['nullable', 'email', 'max:100'],
            'mobile'        => ['nullable', 'string', 'max:30'],
            'gender'        => ['nullable', Rule::in(array_keys(\App\Enums\Gender::options()))],
            'dob'           => ['nullable', 'date'],
            'relation'      => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:3'],
            // Business fields
            'business_name'       => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'tin'                 => ['nullable', 'string', 'max:100'],
            'business_type'       => ['nullable', 'string', 'max:100'],
            'contact_email'       => ['nullable', 'email', 'max:100'],
            'contact_phone'       => ['nullable', 'string', 'max:30'],
            'address_line1_b'     => ['nullable', 'string', 'max:255'],
            'address_line2_b'     => ['nullable', 'string', 'max:255'],
            'city_b'              => ['nullable', 'string', 'max:100'],
            'state_b'             => ['nullable', 'string', 'max:100'],
            'postal_code_b'       => ['nullable', 'string', 'max:20'],
            'country_b'           => ['nullable', 'string', 'max:3'],
        ];

        return $rules;
    }
}
