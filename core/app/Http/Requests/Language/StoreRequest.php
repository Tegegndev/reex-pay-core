<?php

namespace App\Http\Requests\Language;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'flag'          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'language_name' => 'required|string',
            'language_code' => 'unique:languages,code|required|string',
            'is_default'    => 'boolean',
            'is_rtl'        => 'boolean',
            'status'        => 'boolean',
        ];
    }
}
