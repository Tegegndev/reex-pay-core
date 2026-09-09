<?php

namespace App\Http\Controllers\Backend;

use App\Enums\LinkTarget;
use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class SocialController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'                     => 'social-list',
            'store|edit|update|destroy' => 'social-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socials = Social::all();

        return view('backend.social.index', compact('socials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Social::create($this->validatedData($request));

        notifyEvs('success', __('Social Link added successfully.'));

        return redirect()->route('admin.social.index');
    }

    /**
     * Validate and return request data
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'icon_class' => ['required', 'string', 'max:255'],
            'target'     => ['required', new Enum(LinkTarget::class)],
            'url'        => ['required', 'url', 'max:255'],
            'status'     => ['nullable', 'integer', 'in:0,1'],
        ]) + ['status' => $request->boolean('status')];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Social $social)
    {
        return view('backend.social.partials._form_data', compact('social'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Social $social)
    {
        $social->update($this->validatedData($request));

        notifyEvs('success', __('Social Link updated successfully.'));

        return redirect()->route('admin.social.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Social $social)
    {
        $social->delete();

        notifyEvs('success', __('Social Link deleted successfully.'));

        return redirect()->route('admin.social.index');
    }
}
