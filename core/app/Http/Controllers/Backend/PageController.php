<?php

namespace App\Http\Controllers\Backend;

use App\Enums\PageType;
use App\Http\Requests\Page\PageRequest;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageComponent;
use App\Traits\FileManageTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index'        => 'page-list',
            'create|store' => 'page-create',
            'edit|update'  => 'page-edit',
            'destroy'      => 'page-delete',

        ];
    }

    /*
     * Retrieves all pages and renders the page index view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pages = Page::all();

        return view('backend.page.index', compact('pages'));
    }

    /**
     * Renders the page create view.
     *
     * @return View
     */
    public function create()
    {
        $components = PageComponent::active()->where('page_id', null)->get()->sortBy('sort');

        $locales = Language::languageGet();

        return view('backend.page.create', compact('components', 'locales'));
    }

    /**
     * Creates a new page.
     *
     * @return RedirectResponse
     */
    public function store(PageRequest $request)
    {
        $validated = $request->validated();

        $breadcrumb = $request->hasFile('breadcrumb') ? $this->uploadImage($request->file('breadcrumb')) : null;

        // Store the page
        Page::create([
            'title'         => $validated['page_title'],
            'slug'          => Str::slug($validated['page_slug']),
            'component_ids' => $validated['component'],
            'type'          => PageType::Dynamic,
            'is_active'     => $validated['is_active'] ?? false,
            'breadcrumb'    => $breadcrumb,
            'is_breadcrumb' => $validated['is_breadcrumb'] ?? false,
        ]);

        notifyEvs('success', __('Page created successfully.'));

        return redirect()->route('admin.page.site.index');
    }

    /**
     * Renders the page edit view.
     *
     * @return View
     */
    public function edit($id)
    {
        $page = Page::find($id);

        // Load all components to show in list
        $components = PageComponent::active()->where('page_id', $page->id)->get();

        if ($components->isEmpty()) {
            $components = PageComponent::active()->where('page_id', null)->get();
        }

        $locales = Language::languageGet();

        return view('backend.page.edit', compact('page', 'components', 'locales'));
    }

    /**
     * Updates the page.
     *
     * @return RedirectResponse
     */
    public function update(PageRequest $request, $id)
    {
        $page      = Page::find($id);
        $validated = $request->validated();

        if (isset($validated['breadcrumb']) && $validated['breadcrumb'] === 'coevs-remove' && $page->breadcrumb) {
            $this->delete($page->breadcrumb);
            $validated['breadcrumb'] = null;
        } else {
            $breadcrumb = $request->hasFile('breadcrumb') ? $this->uploadImage($request->file('breadcrumb')) : $page->breadcrumb;
        }

        $isActive     = $page->is_home ? true : $validated['is_active']      ?? false;
        $isBreadcrumb = $page->is_home ? false : $validated['is_breadcrumb'] ?? false;

        $page->update([
            'title'         => $validated['page_title'],
            'slug'          => $page->is_protected ? $page->slug : Str::slug($validated['page_slug']),
            'component_ids' => $validated['component'],
            'breadcrumb'    => $breadcrumb ?? null,
            'is_breadcrumb' => $isBreadcrumb,
            'is_active'     => $isActive,
        ]);

        notifyEvs('success', __('Page updated successfully.'));

        return redirect()->route('admin.page.site.index');
    }

    public function destroy($id)
    {
        $page = Page::find($id);

        if ($page->seo) {
            $page->seo->delete();
        }
        if ($page->type === PageType::Static) {
            notifyEvs('error', __('Static pages cannot be deleted.'));

            return redirect()->back();
        }

        if ($page->breadcrumb) {
            $this->delete($page->breadcrumb);
        }

        $page->delete();
        notifyEvs('success', __('Page Deleted Successfully'));

        return redirect()->back();
    }
}
