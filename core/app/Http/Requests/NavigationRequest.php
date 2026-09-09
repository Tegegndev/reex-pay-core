<?php

namespace App\Http\Requests;

use App\Enums\LinkTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class NavigationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id        = $this->route('site');
        $customUrl = (bool) $this->input('custom_url') ?? false;

        return [
            'name'   => 'required|array',
            'name.*' => 'required|string|max:255',

            'custom_url' => 'nullable|boolean',

            'slug' => [
                Rule::requiredIf($customUrl),
                Rule::when($customUrl, ['max:255', Rule::unique('navigations', 'slug')->ignore($id)]),
            ],

            'page_id' => [
                'nullable', Rule::requiredIf(! $customUrl), 'exists:pages,id',
                Rule::unique('navigations', 'page_id')->ignore($id),
            ],

            'target'    => ['required', new Enum(LinkTarget::class)],
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => __('The name for this item is required.'),
            'name.*.required' => __('Please provide the name in each language.'),
            'name.*.string'   => __('Each name must be valid text.'),
            'name.*.max'      => __('Each name must not exceed 255 characters.'),

            'slug.required' => __('The external URL is required when Custom URL is enabled.'),
            'slug.max'      => __('The URL cannot exceed 255 characters.'),

            'page_id.required' => __('Please select a page when Custom URL is disabled.'),
            'page_id.exists'   => __('The selected page does not exist.'),
            'page_id.unique'   => __('The page has already been taken.'),

            'target.required' => __('Please select a target option.'),
            'target.in'       => __('Invalid link target. Choose between Same Tab or New Tab.'),

            'is_active.boolean' => __('Invalid value for status.'),
        ];
    }
}
