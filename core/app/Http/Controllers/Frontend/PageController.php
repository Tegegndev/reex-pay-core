<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageMetaService;

class PageController extends Controller
{
    /**
     * Handle the incoming request (Single Action Controller).
     */
    public function __invoke(string $slug = '/')
    {
        if ($slug === '/' || $slug === 'blog') {
            abort(404);
        }

        $page = Page::findBySlug($slug);

        $meta = PageMetaService::build($page);

        $components   = $page->components;
        $isBreadcrumb = $page->is_breadcrumb;

        $locale = app()->getLocale();

        $isPage = true;

        return view('frontend.pages.index', compact('page', 'isBreadcrumb', 'components', 'meta', 'locale', 'isPage'));
    }
}
