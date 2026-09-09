<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\NavigationRequest;
use App\Models\Language;
use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Http\Request;

class NavigationController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|store|edit|update' => 'navigation-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages       = Page::active()->get();
        $navigations = Navigation::orderBy('order')->get();
        $locales     = Language::languageGet();

        return view('backend.navigation.index', compact('pages', 'navigations', 'locales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NavigationRequest $request)
    {
        $navigation = new Navigation;
        $this->saveNavigation($navigation, $request);

        notifyEvs('success', __('Navigation item created successfully'));

        return redirect()->back();
    }

    /**
     * Common save logic for store and update.
     */
    protected function saveNavigation(Navigation $navigation, NavigationRequest $request): void
    {
        $navigation->name = $request->input('name');

        $navigation->slug = $request->boolean('custom_url')
            ? $request->input('slug')
            : optional(Page::find($request->input('page_id')))->slug;

        $navigation->page_id   = $request->boolean('custom_url') ? null : $request->input('page_id');
        $navigation->target    = $request->input('target', '_self');
        $navigation->is_active = $request->boolean('is_active', true);

        // preserve order for updates or set next order on create
        if (! $navigation->exists) {
            $navigation->order = Navigation::max('order') + 1;
        }

        $navigation->save();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $navigation = Navigation::findOrFail($id);
        $locales    = Language::where('status', true)->pluck('name', 'code')->toArray();
        $pages      = Page::active()->select('id', 'title')->get();

        return view('backend.navigation.partials._edit_append', compact('navigation', 'pages', 'locales'))->render();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $navigation = Navigation::findOrFail($id);
        $navigation->delete();

        notifyEvs('success', __('Navigation deleted successfully'));

        return redirect()->back();
    }

    /**
     * Update the position of multiple navigation items.
     */
    public function positionUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'positions'         => 'required|array',
            'positions.*.id'    => 'required|exists:navigations,id',
            'positions.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->positions as $item) {
            $navigation = Navigation::find($item['id']);
            if ($navigation) {
                $navigation->order = $item['order'];
                $navigation->save(); // save() automatically trigger the saved event!
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => __('Navigation order updated successfully'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NavigationRequest $request, string $id)
    {

        $navigation = Navigation::findOrFail($id);
        $this->saveNavigation($navigation, $request);

        notifyEvs('success', __('Navigation item updated successfully'));

        return redirect()->back();
    }
}
