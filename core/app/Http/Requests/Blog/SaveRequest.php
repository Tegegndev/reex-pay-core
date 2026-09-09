<?php

namespace App\Http\Requests\Blog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveRequest extends FormRequest
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
        $defaultLocale = app()->getDefaultLocale();

        return [
            "title.{$defaultLocale}" => ['required', 'string', 'max:255'],
            'title.*'                => ['nullable', 'string', 'max:255'],

            "excerpt.{$defaultLocale}" => ['required', 'string', 'max:500'],
            'excerpt.*'                => ['nullable', 'string', 'max:500'],

            "content.{$defaultLocale}" => ['required', 'string'],
            'content.*'                => ['nullable', 'string'],

            "meta_title.{$defaultLocale}" => ['required', 'string', 'max:255'],
            'meta_title.*'                => ['nullable', 'string', 'max:255'],

            "meta_description.{$defaultLocale}" => ['required', 'string', 'max:500'],
            'meta_description.*'                => ['nullable', 'string', 'max:500'],

            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:blogs,slug,'.$this->route('post')],
            'category_id'   => ['required', 'exists:blog_categories,id'],
            'thumbnail'     => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'max:2048'],
            'status'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        $defaultLocale = app()->getDefaultLocale();

        return [
            // Title
            "title.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Title']),
            'title.*.string'                  => __('Each title must be a valid text.'),
            'title.*.max'                     => __('Each title must not exceed 255 characters.'),

            // Excerpt
            "excerpt.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Excerpt']),
            'excerpt.*.string'                  => __('Each excerpt must be a valid text.'),
            'excerpt.*.max'                     => __('Each excerpt must not exceed 500 characters.'),

            // Content
            "content.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Content']),
            'content.*.string'                  => __('Each content must be a valid text.'),

            // Meta Title
            "meta_title.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Meta Title']),
            'meta_title.*.string'                  => __('Each meta title must be a valid text.'),
            'meta_title.*.max'                     => __('Each meta title must not exceed 255 characters.'),

            // Meta Description
            "meta_description.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Meta Description']),
            'meta_description.*.string'                  => __('Each meta description must be valid text.'),
            'meta_description.*.max'                     => __('Each meta description must not exceed 500 characters.'),

            // Meta Keywords
            "meta_keywords.{$defaultLocale}.required" => __('The :attribute field is required.', ['attribute' => strtoupper($defaultLocale).' Meta Keywords']),
            'meta_keywords.*.string'                  => __('Each meta keyword must be a valid text.'),
            'meta_keywords.*.max'                     => __('Each meta keyword must not exceed 500 characters.'),

            // Slug
            'slug.string' => __('Slug must be a valid text.'),
            'slug.max'    => __('Slug must not exceed 255 characters.'),
            'slug.unique' => __('This slug has already been taken. Please choose another.'),

            // Category
            'category_id.required' => __('Please select a category.'),
            'category_id.exists'   => __('The selected category is invalid.'),

            // Thumbnail
            'thumbnail.required' => __('Thumbnail image is required.'),
            'thumbnail.image'    => __('Thumbnail must be a valid image file.'),
            'thumbnail.max'      => __('Thumbnail must not exceed 2MB.'),

            // Status
            'status.boolean' => __('Status must be true or false.'),
        ];
    }

    public function attributes(): array
    {
        $defaultLocale = app()->getDefaultLocale();

        return [
            "title.{$defaultLocale}"            => __('Title'),
            "excerpt.{$defaultLocale}"          => __('Excerpt'),
            "content.{$defaultLocale}"          => __('Content'),
            "meta_title.{$defaultLocale}"       => __('Meta Title'),
            "meta_description.{$defaultLocale}" => __('Meta Description'),
            "meta_keywords.{$defaultLocale}"    => __('Meta Keywords'),
            'slug'                              => __('Slug'),
            'category_id'                       => __('Category'),
            'thumbnail'                         => __('Thumbnail Image'),
            'status'                            => __('Status'),
        ];
    }
}
