<?php

namespace App\Http\Controllers\Backend;

use App\Models\UserRank;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;

class UserRankController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|store|edit|update' => 'ranking-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userRanks = UserRank::all();

        return view('backend.user_rank.index', compact('userRanks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name'               => ['required', 'unique:user_ranks,name'],
            'transaction_amount' => ['required', 'numeric'],
            'reward'             => ['required', 'numeric'],
            'transaction_types'  => ['required', 'array'],
            'description'        => ['nullable', 'string'],
            'features'           => ['nullable', 'array'],
            'is_active'          => ['sometimes', 'boolean'],
            'icon'               => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        // Set default for `is_active` if not provided
        $validated['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $this->uploadImage($validated['icon']);
        }

        UserRank::create($validated);
        notifyEvs('success', __('User rank created successfully'));

        return redirect()->route('admin.ranking.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $userRank = UserRank::findOrFail($id);

        return view('backend.user_rank.partials._edit_form', compact('userRank'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $userRank  = UserRank::findOrFail($id);
        $validated = $request->validate([
            'name'               => ['required', 'unique:user_ranks,name,'.$userRank->id],
            'transaction_amount' => ['required_if:is_default,0:', 'numeric'],
            'reward'             => ['required', 'numeric'],
            'transaction_types'  => ['required', 'array'],
            'description'        => ['nullable', 'string'],
            'features'           => ['nullable', 'array'],
            'is_active'          => ['sometimes', 'boolean'],
            'icon'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        // Set default for `is_active` if not provided
        $validated['is_active'] = $request->boolean('is_active', false);

        // Handle icon upload if a new file is provided
        if ($request->hasFile('icon')) {
            $validated['icon'] = $this->uploadImage($validated['icon'], $userRank->icon);
        }

        // not allow to change transaction amount for default rank
        if ($userRank->is_default && isset($validated['transaction_amount'])) {
            $validated['transaction_amount'] = 0;
        }

        // Protect default rank status - always keep active
        if ($userRank->is_default) {
            $validated['is_active'] = true;
        }

        // Update the user rank with validated data
        $userRank->update($validated);

        // Notify and redirect
        notifyEvs('success', __('User rank updated successfully'));

        return redirect()->route('admin.ranking.index');
    }
}
