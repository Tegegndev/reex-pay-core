<?php

namespace App\Http\Requests\Seo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $defaultLocale = app()->getDefaultLocale();
        $seoId         = $this->route('site_seo'); // if update

        $globalExists = \App\Models\SiteSeo::whereNull('page_id')
            ->when($seoId, fn ($q) => $q->where('id', '!=', $seoId))
            ->exists();

        return [
            // 🔹 Meta Title
            "meta_title.{$defaultLocale}" => ['required', 'string', 'max:255'],
            'meta_title.*'                => ['nullable', 'string', 'max:255'],

            // 🔹 Meta Description
            "meta_description.{$defaultLocale}" => ['required', 'string', 'max:500'],
            'meta_description.*'                => ['nullable', 'string', 'max:500'],

            // 🔹 Meta Keywords
            'meta_keywords' => ['nullable', 'string', 'max:500'],

            // 🔹 Canonical & Robots
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots'        => ['nullable', 'string', 'max:255'],

            // 🔹 Page ID Handling
            'page_id' => array_filter([
                $globalExists ? 'required' : 'nullable',
                'integer',
                'exists:pages,id',
                Rule::unique('site_seos', 'page_id')->ignore($seoId),
            ]),
        ];
    }

    public function messages(): array
    {
        $defaultLocale = app()->getDefaultLocale();

        return [
            "meta_title.{$defaultLocale}.required"       => __('Meta Title is required for :locale.', ['locale' => strtoupper($defaultLocale)]),
            "meta_description.{$defaultLocale}.required" => __('Meta Description is required for :locale.', ['locale' => strtoupper($defaultLocale)]),
            'meta_keywords.string'                       => __('Meta Keywords must be a valid string.'),
            'canonical_url.url'                          => __('The Canonical URL must be a valid URL.'),
            'page_id.required'                           => __('Page selection is required unless this is for global SEO.'),
            'page_id.exists'                             => __('The selected page does not exist.'),
            'page_id.unique'                             => __('SEO entry already exists for this page.'),
        ];
    }

    public function attributes(): array
    {
        return [
            'meta_title'       => __('Meta Title'),
            'meta_description' => __('Meta Description'),
            'meta_keywords'    => __('Meta Keywords'),
            'canonical_url'    => __('Canonical URL'),
            'robots'           => __('Robots Meta'),
            'page_id'          => __('Page'),
        ];
    }
}
