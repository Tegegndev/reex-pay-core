<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Mews\Purifier\Facades\Purifier;

trait PurifyTrait
{
    /**
     * Purify multi-language array or single string content.
     *
     * @param string|null $targetFolder example: images/blogs
     */
    public function purifyContent(array|string|null $content, ?string $targetFolder = null): array|string|null
    {
        if (is_array($content)) {
            return collect($content)
                ->map(fn ($value) => Purifier::clean(
                    htmlspecialchars_decode($this->moveSummernoteImages($value, $targetFolder))
                ))
                ->toArray();
        }

        if (is_string($content)) {
            return Purifier::clean(
                htmlspecialchars_decode($this->moveSummernoteImages($content, $targetFolder))
            );
        }

        return $content; // null or unexpected types
    }

    /**
     * Move summernote temp uploaded images to permanent folder.
     */
    private function moveSummernoteImages(?string $content, ?string $targetFolder = null): string
    {

        if (blank($content) || $content == '' || $content == null) {
            return '';
        }

        // Default final upload folder
        $finalFolder = $targetFolder ?? 'images/others';

        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (str_contains($src, 'storage/images/temp/')) {
                $relativePath = str_replace(asset('storage/').'/', '', $src);

                if (Storage::disk('public')->exists($relativePath)) {
                    $filename = basename($relativePath);

                    $todayFolder = now()->format('Y/m/d');
                    $newPath     = "{$finalFolder}/{$todayFolder}/{$filename}";

                    // Create directory if not exists
                    Storage::disk('public')->makeDirectory("{$finalFolder}/{$todayFolder}");

                    // Move file
                    Storage::disk('public')->move($relativePath, $newPath);

                    // Update src attribute in content
                    $img->setAttribute('src', asset('storage/'.$newPath));
                }
            }
        }

        return $dom->saveHTML();
    }
}
