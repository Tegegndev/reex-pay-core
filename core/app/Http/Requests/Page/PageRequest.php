<?php

namespace App\Http\Requests\Page;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('site');
        $page   = Page::find($pageId);
        $isHome = $page && $page->is_home;

        return [
            'page_title'   => 'required|array',
            'page_title.*' => 'required|string|max:255',

            'page_slug' => array_filter([
                'required',
                'string',
                'max:255',
                $isHome ? null : 'alpha_dash', // Allow / for home page only
                Rule::unique('pages', 'slug')->ignore(optional($page)->id),

                function ($attribute, $value, $fail) use ($isHome) {
                    $forbidden = config('forbidden_slugs.pages', ['home']);

                    // 👉 Skip forbidden slug check for Home Page
                    if (! $isHome && in_array(strtolower($value), $forbidden, true)) {
                        $fail(__('This slug is reserved and cannot be used.'));
                    }

                    // 👉 If this is Home Page, slug must stay exactly '/'
                    if ($isHome && $value !== '/') {
                        $fail(__('The home page slug cannot be changed.'));
                    }
                },
            ]),

            'breadcrumb' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== 'coevs-remove') {
                        // Only validate as image if it's not 'coevs-remove'
                        $validator = \Validator::make(
                            [$attribute => $value],
                            [$attribute => 'image|mimes:jpg,jpeg,png|max:2048']
                        );

                        if ($validator->fails()) {
                            $fail($validator->errors()->first($attribute));
                        }
                    }
                },
            ],            'is_breadcrumb' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            'component'   => ['required', 'array'],
            'component.*' => ['integer', 'exists:page_components,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'page_title'    => __('Page Title'),
            'page_slug'     => __('Page Slug'),
            'component'     => __('Page Components'),
            'is_breadcrumb' => __('Breadcrumb Visibility'),
            'is_active'     => __('Active Status'),
        ];
    }

    public function messages(): array
    {
        return [
            'page_title.required'   => __('The title for this page is required.'),
            'page_title.*.required' => __('Please provide the title for each language of this page.'),
            'page_title.*.string'   => __('Each page title must be a valid string.'),
            'page_title.*.max'      => __('Each page title must not exceed 255 characters.'),

            'page_slug.alpha_dash' => __('The slug may only contain letters, numbers, dashes and underscores.'),
            'component.required'   => __('You must select at least one page component.'),
            'component.*.exists'   => __('Selected component does not exist.'),
        ];
    }
}
