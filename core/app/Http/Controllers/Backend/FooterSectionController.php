<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Footer\SectionRequest;
use App\Models\FooterSection;
use App\Models\Language;
use Illuminate\Http\Request;

class FooterSectionController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index' => 'page-footer-manage',
        ];
    }

    public function index()
    {
        $locales  = Language::languageGet();
        $sections = FooterSection::orderBy('order')->get();

        return view('backend.page_footer.sections.index', compact('sections', 'locales'));
    }

    public function store(SectionRequest $request)
    {

        $input = $request->all();

        FooterSection::create([
            'title'  => $input['title'],
            'type'   => $input['type'],
            'status' => $request->boolean('status', false),
            'order'  => FooterSection::max('order') + 1,
        ]);

        notifyEvs('success', __('Footer section created successfully'));

        return redirect()->back();
    }

    public function edit($id)
    {
        $footerSection = FooterSection::findOrFail($id);
        $locales       = Language::languageGet();
        $sections      = FooterSection::orderBy('order')->get();

        return view('backend.page_footer.sections.partials._edit_data', compact('footerSection', 'locales', 'sections'))->render();
    }

    public function positionUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'positions'         => 'required|array',
            'positions.*.id'    => 'required|exists:footer_sections,id',
            'positions.*.order' => 'required|integer|min:1',
        ]);

        foreach ($request->positions as $item) {
            $footerSection = FooterSection::find($item['id']);
            if ($footerSection) {
                $footerSection->order = $item['order'];
                $footerSection->save(); // save() automatically trigger the saved event!
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => __('Footer Section order updated successfully'),
        ]);
    }

    public function update(SectionRequest $request, $id)
    {
        $input = $request->all();

        $footerSection = FooterSection::findOrFail($id);
        $footerSection->update([
            'title'  => $input['title'],
            'type'   => $input['type'],
            'status' => $request->boolean('status', false),
        ]);

        notifyEvs('success', __('Footer section updated successfully'));

        return redirect()->back();
    }

    public function destroy($id)
    {

        $footerSection = FooterSection::findOrFail($id);

        // Delete the items associated with the footer section
        $footerSection->items()->delete();
        $footerSection->delete();

        notifyEvs('success', __('Footer section deleted successfully'));

        return redirect()->route('admin.page.footer.section.index');
    }
}
