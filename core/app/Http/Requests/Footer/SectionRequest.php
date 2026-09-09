<?php

namespace App\Http\Requests\Footer;

use App\Enums\FooterSectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            "title.{$defaultLocale}" => 'required|string|max:255',
            'title.*'                => 'nullable|string|max:255',

            'type'   => ['required', new Enum(FooterSectionType::class)],
            'status' => 'boolean',
            'order'  => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            "title.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' title']),
            'title.*.string'                  => __('Each title must be a valid string.'),
            'title.*.max'                     => __('Each title must not exceed 255 characters.'),
            'type.required'                   => __('Section type is required.'),
            'type.enum'                       => __('The selected section type is invalid.'),
        ];
    }

    public function attributes(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            "title.{$defaultLocale}" => __('title'),
            'type'                   => __('section type'),
        ];
    }
}
