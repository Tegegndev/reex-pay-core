<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Seo\SiteSeoRequest;
use App\Models\Language;
use App\Models\Page;
use App\Models\SiteSeo;
use App\Traits\FileManageTrait;

class SiteSeoController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|store|create|edit|update|destroy' => 'seo-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seos = SiteSeo::paginate(10);

        return view('backend.site_seo.index', compact('seos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteSeoRequest $request)
    {
        $validated = $request->validated();

        // Handle meta_keywords
        $validated['meta_keywords'] = $this->processMetaKeywords($validated['meta_keywords'] ?? null);

        // If SEO image uploaded
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }

        SiteSeo::create($validated);

        notifyEvs('success', __('SEO settings created successfully.'));

        return redirect()->route('admin.site-seo.index');
    }

    /**
     * Process the meta keywords.
     */
    private function processMetaKeywords(?string $metaKeywords): ?string
    {
        if (blank($metaKeywords)) {
            return null;
        }

        $decoded = json_decode($metaKeywords, true);

        if (is_array($decoded)) {
            $keywords = collect($decoded)
                ->pluck('value')
                ->filter()
                ->implode(',');

            return $keywords ?: null;
        }

        return $metaKeywords; // fallback if it's already string
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locales = Language::languageGet();
        $pages   = Page::all();

        return view('backend.site_seo.create', compact('locales', 'pages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seo     = SiteSeo::findOrFail($id);
        $locales = Language::languageGet();
        $pages   = Page::all();

        return view('backend.site_seo.edit', compact('seo', 'locales', 'pages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteSeoRequest $request, $id)
    {
        $seo = SiteSeo::findOrFail($id);

        $validated = $request->validated();

        // Handle meta_keywords
        $validated['meta_keywords'] = $this->processMetaKeywords($validated['meta_keywords'] ?? null);

        // If SEO image uploaded
        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploadImage($request->file('image'));
        }
        $seo->update($validated);

        notifyEvs('success', __('SEO settings updated successfully.'));

        return redirect()->route('admin.site-seo.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $seo = SiteSeo::findOrFail($id);

        if ($seo->isGlobal()) {
            notifyEvs('error', __('You cannot delete global SEO settings.'));

            return redirect()->route('admin.site-seo.index');
        }

        $seo->delete();
        notifyEvs('success', __('SEO settings deleted successfully.'));

        return redirect()->route('admin.site-seo.index');
    }
}
