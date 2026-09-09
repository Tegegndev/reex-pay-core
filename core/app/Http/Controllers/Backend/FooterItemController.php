<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Footer\ItemRequest;
use App\Models\FooterItem;
use App\Models\FooterSection;
use App\Models\Language;
use App\Models\Page;
use App\Models\Social;
use Illuminate\Http\Request;

class FooterItemController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index' => 'page-footer-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $footerSection = FooterSection::with('items')->findOrFail(request('footer_section'));
        $locales       = Language::languageGet();
        $pages         = Page::active()->get();
        $socials       = Social::active()->get();

        return view('backend.page_footer.items.index', compact('footerSection', 'locales', 'pages', 'socials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request)
    {
        $input     = $request->validated();
        $sectionId = $input['section_id'] ?? null;

        $footerSection = FooterSection::findOrFail($sectionId);

        $footerSection->items()->create([
            'label'     => $input['label'],
            'content'   => $input['content'] ?? null,
            'url_type'  => $input['url_type'],
            'url'       => $input['url']       ?? null,
            'page_id'   => $input['page_id']   ?? null,
            'social_id' => $input['social_id'] ?? null,
            'order'     => FooterItem::where('footer_section_id', $footerSection->id)->max('order') + 1,
            'icon'      => $input['icon'] ?? null,
            'status'    => $request->boolean('status'),
        ]);

        notifyEvs('success', __('Footer item created successfully'));

        return redirect()->route('admin.page.footer.item.index', ['footer_section' => $sectionId]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $footerItem = FooterItem::findOrFail($id);
        $locales    = Language::languageGet();
        $pages      = Page::active()->get();
        $socials    = Social::active()->get();

        return view('backend.page_footer.items.partials._form_data', compact('footerItem', 'locales', 'pages', 'socials'))->render();
    }

    /**
     * Update the order of the footer items.
     */
    public function positionUpdate(Request $request)
    {
        $request->validate([
            'positions'         => 'required|array',
            'positions.*.id'    => 'required|exists:footer_Items,id',
            'positions.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->positions as $item) {
            FooterItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => __('Footer Item order updated successfully'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemRequest $request, $id)
    {
        $input      = $request->validated();
        $footerItem = FooterItem::findOrFail($id);

        $footerItem->update([
            'label'     => $input['label'],
            'content'   => $input['content'] ?? null,
            'url_type'  => $input['url_type'],
            'url'       => $input['url']       ?? null,
            'page_id'   => $input['page_id']   ?? null,
            'social_id' => $input['social_id'] ?? null,
            'icon'      => $input['icon']      ?? null,
            'status'    => $request->boolean('status'),
        ]);

        notifyEvs('success', __('Footer item updated successfully'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $footerItem = FooterItem::findOrFail($id);
        $footerItem->delete();
        notifyEvs('success', __('Footer item deleted successfully'));

        return redirect()->back();
    }
}
