<?php

namespace App\Services;

use App\Models\Setting;
use App\Traits\FileManageTrait;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

class SettingService
{
    use FileManageTrait;
    use ValidatesRequests;

    /**
     * Update the settings for a given section.
     *
     * @param string  $section The section of the settings.
     * @param Request $request The request object.
     */
    public function update(string $section, Request $request): void
    {
        // Get the validation rules for the given section.
        $rules = Setting::getValidationRules($section);

        // Validate the request data.
        $data = $this->validate($request, $rules);

        // Get the valid settings keys.
        $validSettings = array_keys($rules);

        // Update the settings.
        foreach ($data as $key => $val) {
            // Check if the key is a valid setting.
            if (in_array($key, $validSettings, true)) {

                // Check if the request has a file for the key.
                if ($request->hasFile($key)) {
                    // Get the old image for the key.
                    $oldImage = Setting::get($key, $section);

                    // Upload the new image and get the path.
                    $val = self::uploadImage($val, $oldImage);
                }

                // Add the setting.
                Setting::add($key, $val, Setting::getDataType($key, $section));
            }
        }
    }
}
