<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteSeo;

class PageMetaService
{
    /**
     * Build structured SEO meta data and page info.
     *
     * @param \App\Models\Blog|null $blog
     */
    public static function build(Page $page, $blog = null): array
    {
        $locale         = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        $siteName       = setting('site_title') ?? config('app.name');
        $globalSeo      = SiteSeo::global();

        $seo = $page->seo ?? $globalSeo;

        $title       = $seo?->meta_title[$locale]       ?? $seo?->meta_title[$fallbackLocale] ?? $page->title[$locale] ?? $page->title[$fallbackLocale] ?? $siteName;
        $description = $seo?->meta_description[$locale] ?? $seo?->meta_description[$fallbackLocale] ?? '';
        $keywords    = is_array($seo?->meta_keywords) ? implode(',', $seo->meta_keywords) : ($seo?->meta_keywords ?? '');
        $image       = $seo?->image ? asset($seo->image) : '';

        // If blog detail passed, override SEO values
        if ($blog) {
            $title       = $blog->meta_title[$locale]       ?? $title;
            $description = $blog->meta_description[$locale] ?? $description;
            $keywords    = is_array($blog->meta_keywords) ? implode(',', $blog->meta_keywords) : ($blog->meta_keywords ?? $keywords);
            $image       = $blog->thumbnail ? asset($blog->thumbnail) : $image;
        }

        return [
            'meta' => [
                'title'         => $title,
                'description'   => $description,
                'keywords'      => $keywords,
                'canonical_url' => $seo?->canonical_url ?? url()->current(),
                'robots'        => $seo?->robots        ?? 'index, follow',
                'image'         => $image,
                'og'            => [
                    'site_name'   => $siteName,
                    'title'       => $title,
                    'description' => $description,
                    'type'        => 'website',
                    'url'         => url()->current(),
                    'image'       => $image,
                    'locale'      => str_replace('_', '-', $locale),
                ],
                'twitter' => [
                    'card'        => 'summary_large_image',
                    'title'       => $title,
                    'description' => $description,
                    'image'       => $image,
                    'site'        => setting('twitter_username') ?? '',
                ],
            ],
            'components'   => $page->components,
            'isBreadcrumb' => $page->is_breadcrumb,
        ];
    }
}
