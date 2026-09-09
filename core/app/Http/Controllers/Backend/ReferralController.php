<?php

namespace App\Http\Controllers\Backend;

use App\Models\Language;
use App\Models\ReferralContent;
use App\Models\Reward;
use App\Models\Setting;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;

class ReferralController extends BaseController
{
    use FileManageTrait;

    public static function permissions(): array
    {
        return [
            'index|store|edit|update|statusUpdate|destroy|contentUpdate|cardContent' => 'referral-manage',
        ];
    }

    public function index()
    {
        $referralRewards = Reward::all()->groupBy('type');

        return view('backend.referral.index', compact('referralRewards'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'reward' => 'required',
            'type'   => 'required',
        ]);

        $level = Reward::where('type', $validated['type'])->max('level') + 1;

        Reward::create([
            'percentage' => $validated['reward'],
            'type'       => $validated['type'],
            'level'      => $level,
        ]);
        notifyEvs('success', 'Reward created successfully');

        return redirect()->route('admin.referral.index');
    }

    public function statusUpdate($type, $status)
    {
        $key     = $type.'_rewards';
        $section = 'hide_settings';
        Setting::add($key, $status, Setting::getDataType($key, $section));

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);

        return view('backend.referral.edit', compact('reward'))->render();
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'reward' => 'required',
        ]);

        $reward = Reward::findOrFail($id);
        $reward->update([
            'percentage' => $validated['reward'],
        ]);
        notifyEvs('success', 'Reward updated successfully');

        return redirect()->route('admin.referral.index');

    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);

        // Fetch all rewards of the same type except the one being deleted
        $otherRewards = Reward::where('type', $reward->type)
            ->where('id', '!=', $reward->id)
            ->orderBy('level')
            ->get();

        // Rearrange the levels to maintain consecutive order
        foreach ($otherRewards as $index => $otherReward) {
            $otherReward->update(['level' => $index + 1]);
        }

        // Delete the selected reward
        $reward->delete();

        notifyEvs('success', 'Reward deleted and levels rearranged successfully');

        return redirect()->route('admin.referral.index');
    }

    public function cardContent()
    {
        // Get referral content (always exists)
        $referralContent = ReferralContent::getContent();

        // Get available languages from database
        $availableLocales = Language::where('status', true)->pluck('name', 'code')->toArray();

        return view('backend.referral.card_content', compact('referralContent', 'availableLocales'));
    }

    public function contentUpdate(Request $request)
    {
        // Validate multi-language heading (at least English is required)
        $validated = $request->validate([
            'heading'                  => 'required|array',
            'heading.en'               => 'required|string|max:255',
            'heading.*'                => 'nullable|string|max:255',
            'positive_guidelines'      => 'required|array',
            'positive_guidelines.*'    => 'array',
            'positive_guidelines.*.*'  => 'nullable|string|max:500',
            'negative_guidelines'      => 'required|array',
            'negative_guidelines.*'    => 'array',
            'negative_guidelines.*.*'  => 'nullable|string|max:500',
            'referral_card_image_file' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // Get referral content (always exists)
        $referralContent = ReferralContent::getContent();

        // Process multi-language heading
        $headings = [];
        foreach ($validated['heading'] as $lang => $heading) {
            if (! empty($heading)) {
                $headings[$lang] = $heading;
            }
        }
        $referralContent->heading = $headings;

        // Process multi-language positive guidelines
        $positiveGuidelines = [];
        foreach ($validated['positive_guidelines'] as $lang => $guidelines) {
            $cleanGuidelines = array_filter($guidelines, function ($guideline) {
                return ! empty(trim($guideline));
            });
            if (! empty($cleanGuidelines)) {
                $positiveGuidelines[$lang] = array_values($cleanGuidelines);
            }
        }
        $referralContent->positive_guidelines = $positiveGuidelines;

        // Process multi-language negative guidelines
        $negativeGuidelines = [];
        foreach ($validated['negative_guidelines'] as $lang => $guidelines) {
            $cleanGuidelines = array_filter($guidelines, function ($guideline) {
                return ! empty(trim($guideline));
            });
            if (! empty($cleanGuidelines)) {
                $negativeGuidelines[$lang] = array_values($cleanGuidelines);
            }
        }
        $referralContent->negative_guidelines = $negativeGuidelines;

        // Handle file upload using FileManageTrait
        if ($request->hasFile('referral_card_image_file')) {
            $oldImagePath                = $referralContent->image_path ? str_replace('storage/', '', $referralContent->image_path) : null;
            $imagePath                   = $this->uploadImage($request->file('referral_card_image_file'), $oldImagePath);
            $referralContent->image_path = $imagePath;
        }

        // Save the referral content
        $referralContent->save();

        notifyEvs('success', __('Referral content updated successfully!'));

        return redirect()->back();
    }
}
