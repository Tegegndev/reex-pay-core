<?php

namespace App\Http\Requests\Blog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            "name.{$defaultLocale}" => ['required', 'string', 'max:255'],
            'name.*'                => ['nullable', 'string', 'max:255'],
            'slug'                  => ['nullable', 'string', 'max:255', 'unique:blog_categories,slug,'.$this->route('category')],
            'status'                => ['boolean'],
        ];
    }
}
