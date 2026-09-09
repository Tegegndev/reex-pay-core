<?php

namespace App\Http\Requests\SendMoney;

use Illuminate\Foundation\Http\FormRequest;

class SendMoneyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient' => ['required'],
            'wallet_id' => ['required', 'exists:wallets,id'],
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'note'      => ['nullable', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient.required' => __('Please provide a recipient.'),
            'wallet_id.exists'   => __('The selected wallet is invalid.'),
            'amount.min'         => __('The amount must be at least :min.'),
            'note.max'           => __('The note must not exceed :max characters.'),

        ];
    }
}
