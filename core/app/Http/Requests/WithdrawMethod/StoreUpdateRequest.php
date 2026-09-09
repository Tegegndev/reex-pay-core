<?php

namespace App\Http\Requests\WithdrawMethod;

use App\Enums\FixPctType;
use Illuminate\Foundation\Http\FormRequest;

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
            'method_code'        => 'required_if:type,manual',
            'currency'           => 'required',
            'currency_symbol'    => 'required',

            // New charge fields for user and merchant
            'user_charge'          => 'required|numeric|min:0',
            'user_charge_type'     => 'required|in:'.FixPctType::FIXED->value.','.FixPctType::PERCENT->value,
            'merchant_charge'      => 'required|numeric|min:0',
            'merchant_charge_type' => 'required|in:'.FixPctType::FIXED->value.','.FixPctType::PERCENT->value,

            // Old charge fields - made optional for backward compatibility
            'charge'      => 'nullable|numeric|min:0',
            'charge_type' => 'nullable|in:'.FixPctType::FIXED->value.','.FixPctType::PERCENT->value,

            'conversion_rate_live' => 'boolean',
            'conversion_rate'      => 'required_if:conversion_rate_live,0',
            'min_withdraw'         => 'required',
            'max_withdraw'         => 'required',
            'fields'               => 'required_if:type,manual',
            'process_time_value'   => 'required_if:type,manual',
            'process_time_unit'    => 'required_if:type,manual',
            'status'               => 'boolean',
        ];
    }
}
