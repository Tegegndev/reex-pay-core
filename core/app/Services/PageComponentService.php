<?php

namespace App\Services;

use App\Enums\ComponentType;
use App\Models\Language;
use App\Models\PageComponent;
use App\Models\PageComponentRepeatedContent;
use App\Traits\FileManageTrait;
use App\Traits\PurifyTrait;
use Illuminate\Support\Str;

class PageComponentService
{
    use FileManageTrait, PurifyTrait;

    public function getAvailableLocales(): array
    {
        return Language::where('status', true)->pluck('name', 'code')->toArray();
    }

    public function getComponentKey(PageComponent $component): string
    {
        return $component->type === ComponentType::Dynamic ? 'dynamic' : $component->component_key;
    }

    public function getValidationRules(array $fields, array $locales, PageComponent|PageComponentRepeatedContent|null $component = null): array
    {
        $rules      = [];
        $attributes = [];

        foreach ($fields as $field => $meta) {
            $type           = $meta['type'] ?? 'text';
            $isTranslatable = ($type !== 'img') && ($meta['translatable'] ?? false);
            $validationRule = $meta['validation'] ?? 'nullable';
            $label          = ucfirst(str_replace('_', ' ', $field)); // For attribute label

            if ($type === 'img') {
                $fieldKey      = "content_data.$field";
                $existingImage = $component?->content_data[$field] ?? null;
                $shouldRemove  = request()->input($fieldKey) === 'coevs-remove';

                if ($shouldRemove) {
                    continue;
                }

                if (! $component && ! $existingImage) {
                    $rules[$fieldKey] = $validationRule;
                } elseif (! request()->hasFile($fieldKey) && $existingImage) {
                    $validationRule = preg_replace('/\brequired\|?/', '', $validationRule);
                    if (! str_contains($validationRule, 'nullable')) {
                        $validationRule = 'nullable|'.ltrim($validationRule, '|');
                    }
                    $rules[$fieldKey] = $validationRule;
                } else {
                    $rules[$fieldKey] = $validationRule;
                }

                $attributes[$fieldKey] = $label;

            } elseif ($isTranslatable) {
                foreach ($locales as $code => $localeName) {
                    $key              = "content_data.$field.$code";
                    $rules[$key]      = $validationRule;
                    $attributes[$key] = "$label (".strtoupper($code).')';
                }
            } else {
                $key              = "content_data.$field";
                $rules[$key]      = $validationRule;
                $attributes[$key] = $label;
            }
        }

        return compact('rules', 'attributes');
    }

    public function buildContentData(array $fieldDefinitions, array $inputData, array $availableLocales, ?array $existingContent = []): array
    {
        $content = [];

        $availableLocales = array_keys($availableLocales);
        foreach ($fieldDefinitions as $field => $meta) {
            $type           = $meta['type'] ?? 'text';
            $isTranslatable = ($type !== 'img') && ($meta['translatable'] ?? false);

            if ($type === 'img') {
                $fieldKey      = "content_data.$field";
                $existingImage = $existingContent[$field] ?? null;
                $shouldRemove  = $inputData[$field]       ?? null;

                if (request()->hasFile($fieldKey)) {
                    $content[$field] = self::uploadImage(request()->file($fieldKey), $existingImage);
                } elseif ($shouldRemove === 'coevs-remove') {
                    if (Str::contains($meta['validation'] ?? '', 'required')) {
                        notifyEvs('error', __('This image is required and cannot be removed.'));
                        $content[$field] = $existingImage;
                    } else {
                        self::delete($existingImage);
                        $content[$field] = null;
                    }
                } else {
                    $content[$field] = $existingImage;
                }

            } elseif ($isTranslatable && $type === 'text_editor') {
                foreach ($availableLocales as $code) {
                    $content[$field][$code] = $this->purifyContent($inputData[$field][$code] ?? null, 'images/page_components');
                }
            } elseif ($isTranslatable) {
                foreach ($availableLocales as $code) {
                    $content[$field][$code] = $inputData[$field][$code] ?? '';
                }

            } else {
                $content[$field] = $inputData[$field] ?? '';
            }
        }

        return $content;
    }

    public function deleteComponentAssets(PageComponent|PageComponentRepeatedContent $component): void
    {
        // Only PageComponent has component_icon
        if ($component instanceof PageComponent && $component->component_icon) {
            $this->delete($component->component_icon);
        }

        foreach ($component->content_data ?? [] as $field => $value) {
            $this->deleteSummernoteImages($value);
            if (is_string($value) && Str::startsWith($value, 'images/')) {
                $this->delete($value);
            }
        }
    }
}
