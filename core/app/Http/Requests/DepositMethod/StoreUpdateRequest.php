<?php

namespace App\Http\Requests\DepositMethod;

use App\Enums\FixPctType;
use App\Enums\MethodType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'logo'               => 'sometimes|required_if:type,manual|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type'               => 'required',
            'name'               => 'required',
            'payment_gateway_id' => 'required_if:type,auto',
            'method_code'        => 'required_if:type,'.MethodType::MANUAL->value,
            'currency'           => [
                'required',
                Rule::unique('deposit_methods')
                    ->where(function ($query) {
                        return $query->where('payment_gateway_id', $this->payment_gateway_id);
                    })->ignore($this->route('method')), // for update
            ],
            'currency_symbol'      => 'required',
            'charge'               => 'nullable|numeric|min:0',
            'charge_type'          => 'nullable|in:'.implode(',', array_column(FixPctType::cases(), 'value')),
            'user_charge'          => 'nullable|numeric|min:0',
            'user_charge_type'     => 'nullable|in:'.implode(',', array_column(FixPctType::cases(), 'value')),
            'merchant_charge'      => 'nullable|numeric|min:0',
            'merchant_charge_type' => 'nullable|in:'.implode(',', array_column(FixPctType::cases(), 'value')),
            'conversion_rate_live' => 'boolean',
            'conversion_rate'      => 'required_if:conversion_rate_live,0',
            'min_deposit'          => 'required',
            'max_deposit'          => 'required',
            'fields'               => 'required_if:type,manual',
            'status'               => 'boolean',
        ];
    }
}
