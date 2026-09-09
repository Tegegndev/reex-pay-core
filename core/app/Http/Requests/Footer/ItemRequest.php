<?php

namespace App\Http\Requests\Footer;

use App\Enums\FooterItemUrlType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            'section_id' => 'required|integer|exists:footer_sections,id',

            "label.{$defaultLocale}" => 'required|string|max:255',
            'label.*'                => 'nullable|string|max:255',

            "content.{$defaultLocale}" => 'required_if:url_type,'.FooterItemUrlType::NONE->value.'|string|max:1024',
            'content.*'                => 'nullable|string|max:1024',

            'url_type' => ['required', new Enum(FooterItemUrlType::class)],

            'url'       => 'nullable|required_if:url_type,'.FooterItemUrlType::EXTERNAL_URL->value.'|url|max:255',
            'page_id'   => 'nullable|required_if:url_type,'.FooterItemUrlType::PAGE->value.'|integer|exists:pages,id',
            'social_id' => 'nullable|required_if:url_type,'.FooterItemUrlType::SOCIAL->value.'|integer|exists:socials,id',

            'icon'   => 'nullable|string|max:64',
            'order'  => 'nullable|integer',
            'status' => 'boolean',
        ];
    }

    public function messages(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            "label.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' label']),
            'label.*.string'                  => __('Each label must be a valid string.'),
            'label.*.max'                     => __('Each label must not exceed 255 characters.'),

            "content.{$defaultLocale}.required_if" => __('The :attribute field is required when URL Type is Content Only (None).'),
            'content.*.string'                     => __('Each content must be a valid string.'),
            'content.*.max'                        => __('Each content must not exceed 1024 characters.'),

            'url_type.required' => __('The URL Type field is required.'),
            'url_type.enum'     => __('The selected URL Type is invalid.'),

            'url.required_if' => __('The URL field is required when URL Type is External URL.'),
            'url.url'         => __('The URL must be a valid URL.'),

            'page_id.required_if' => __('The Page field is required when URL Type is Page.'),
            'page_id.integer'     => __('The Page field must be a valid page.'),
            'page_id.exists'      => __('The selected page does not exist.'),

            'social_id.required_if' => __('The Social Platform field is required when URL Type is Social.'),
            'social_id.integer'     => __('The Social Platform field must be valid.'),
            'social_id.exists'      => __('The selected social platform does not exist.'),
        ];
    }

    public function attributes(): array
    {
        $defaultLocale = config('app.default_language');

        return [
            "label.{$defaultLocale}"   => __('Label ('.strtoupper($defaultLocale).')'),
            "content.{$defaultLocale}" => __('Content ('.strtoupper($defaultLocale).')'),

            'url_type'  => __('URL Type'),
            'url'       => __('URL'),
            'page_id'   => __('Page'),
            'social_id' => __('Social Platform'),
        ];
    }
}
