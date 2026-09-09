<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ComponentType;
use App\Models\PageComponent;
use App\Services\PageComponentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageComponentController extends BaseController
{
    protected PageComponentService $componentService;

    public function __construct(PageComponentService $componentService)
    {
        $this->componentService = $componentService;
    }

    public static function permissions(): array
    {
        return [
            'index'                            => 'component-list',
            'create|store|edit|update|destroy' => 'component-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $components = PageComponent::all();

        return view('backend.page_component.index', compact('components'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages        = $this->componentService->getAvailableLocales();
        $fieldDefinitions = PageComponent::contentFields('dynamic');

        return view('backend.page_component.create', compact('fieldDefinitions', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'component_icon' => 'required|image|mimes:jpg,jpeg,png,webp',
            'component_name' => 'required|string|max:255',
            'is_active'      => 'boolean',
        ]);

        $fieldDefinitions = PageComponent::contentFields('dynamic');
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

        $component                 = new PageComponent;
        $component->component_icon = $this->componentService->uploadImage($request->file('component_icon'));
        $component->type           = ComponentType::Dynamic;
        $component->component_name = $request->input('component_name');
        $component->component_key  = Str::slug($request->input('component_name'), '_');
        $component->is_active      = $request->boolean('is_active', true);
        $component->content_data   = $contentData;
        $component->save();

        notifyEvs('success', __('Component created successfully.'));

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $component = PageComponent::findOrFail($id);
        $languages = $this->componentService->getAvailableLocales();

        $componentKey     = $this->componentService->getComponentKey($component);
        $fieldDefinitions = PageComponent::contentFields($componentKey);
        $fieldValues      = $component->content_data ?? [];

        return view('backend.page_component.edit', compact('component', 'fieldDefinitions', 'fieldValues', 'languages', 'componentKey'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $component = PageComponent::findOrFail($id);

        $request->validate([
            'component_icon' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'component_name' => 'required|string|max:255',
            'is_active'      => 'boolean',
        ]);

        $componentKey     = $this->componentService->getComponentKey($component);
        $fieldDefinitions = PageComponent::contentFields($componentKey);
        $availableLocales = $this->componentService->getAvailableLocales();

        $validation = $this->componentService->getValidationRules($fieldDefinitions, $availableLocales, $component);

        $request->validate(
            $validation['rules'],
            [], // Optional custom messages
            $validation['attributes'] // 👈 user-friendly field names
        );

        $contentData = $this->componentService->buildContentData(
            $fieldDefinitions,
            $request->input('content_data', []),
            $availableLocales,
            $component->content_data ?? []
        );

        if ($component->type === ComponentType::Dynamic && $request->hasFile('component_icon')) {
            $component->component_icon = $this->componentService->uploadImage($request->file('component_icon'), $component->component_icon);
        }

        $component->component_name = $request->input('component_name');
        $component->is_active      = $request->boolean('is_active');
        $component->content_data   = $contentData;
        $component->save();

        notifyEvs('success', __('Component updated successfully.'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $component = PageComponent::findOrFail($id);

        if ($component->type === ComponentType::Dynamic) {
            $this->componentService->deleteComponentAssets($component);
            $component->delete();

            notifyEvs('success', __('Component deleted successfully.'));
        } else {
            notifyEvs('error', __('Cannot delete this component.'));
        }

        return redirect()->back();
    }
}
