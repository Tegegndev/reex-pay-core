<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function setLocale($locale)
    {
        // Retrieve language by code
        $language = Language::where('code', $locale)->first();

        if (! $language) {
            notifyEvs('error', __('Invalid Language'));

            return redirect()->back();
        }

        // Store locale and direction in session
        session(['locale' => $locale, 'dir' => $language->is_rtl ? 'rtl' : 'ltr']);

        App::setLocale($locale); // Force locale for this request

        notifyEvs('success', __('Language Changed'));

        return redirect()->back();

    }
}
