<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\PageComponent;
use App\Models\PageComponentRepeatedContent;
use App\Services\PageComponentService;
use Illuminate\Http\Request;

class PageComponentRepeatedContentController extends Controller
{
    protected PageComponentService $componentService;

    public function __construct(PageComponentService $componentService)
    {
        $this->componentService = $componentService;
    }

    public static function permissions(): array
    {
        return [
            'store|edit|update|destroy' => 'component-manage',
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'component_id' => 'required',
        ]);

        $component = PageComponent::find($request->input('component_id'));

        // Create a new instance and assign context
        $repeatedContent               = new PageComponentRepeatedContent;
        $repeatedContent->component_id = $component->id;

        if ($component->limitRepeatedContentsOver()) {
            notifyEvs('error', __('Data limit reached to maintain optimal design.'));

            return redirect()->back();
        }

        $fieldDefinitions = PageComponent::contentFields($component->component_key, 'repeated_content');
        $availableLocales = $this->componentService->getAvailableLocales();

        $validation = $this->componentService->getValidationRules($fieldDefinitions, $availableLocales);
        $request->validate(
            $validation['rules'],
            [], // Optional custom messages
            $validation['attributes'] // 👈 user-friendly field names
        );

        $contentData = $this->componentService->buildContentData(
            $fieldDefinitions,
            $request->input('content_data', []),
            $availableLocales
        );

        $repeatedContent->content_data = $contentData;
        $repeatedContent->save();

        notifyEvs('success', __('Repeatable Component Content created successfully.'));

        return redirect()->back();
    }

    public function edit($id)
    {
        $content = PageComponentRepeatedContent::findOrFail($id);

        $component = $content->pageComponent;

        $repeatedFieldDefinitions = PageComponent::contentFields($component->component_key, 'repeated_content');
        $locales                  = Language::languageGet();

        return view('backend.page_component.repeatable_content.modal._form_data', compact('content', 'repeatedFieldDefinitions', 'locales'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $repeatedComponentContent = PageComponentRepeatedContent::findOrFail($id);

        $component = $repeatedComponentContent->pageComponent;

        $fieldDefinitions = PageComponent::contentFields($component->component_key, 'repeated_content');
        $availableLocales = $this->componentService->getAvailableLocales();

        $validation = $this->componentService->getValidationRules($fieldDefinitions, $availableLocales, $repeatedComponentContent);

        $request->validate(
            $validation['rules'],
            [], // Optional custom messages
            $validation['attributes'] // 👈 user-friendly field names
        );

        $contentData = $this->componentService->buildContentData(
            $fieldDefinitions,
            $request->input('content_data', []),
            $availableLocales,
            $repeatedComponentContent->content_data ?? []
        );

        $repeatedComponentContent->content_data = $contentData;
        $repeatedComponentContent->save();

        notifyEvs('success', __('Repeatable Component Content updated successfully.'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $repeatedComponentContent = PageComponentRepeatedContent::findOrFail($id);
        $this->componentService->deleteComponentAssets($repeatedComponentContent);
        $repeatedComponentContent->delete();

        notifyEvs('success', __('Repeatable Component Content deleted successfully.'));

        return redirect()->back();
    }
}
