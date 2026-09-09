<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Traits\FileManageTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Storage;

class SummernoteController extends Controller implements HasMiddleware
{
    use FileManageTrait;

    // ✅ Trait Use!

    public static function middleware()
    {
        return ['auth:admin'];
    }

    public function imageUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'],
        ]);

        // ✅ Use your Trait method
        $path = $this->uploadImage($request->file('file'), isTemp: true);

        return response()->json([
            'url' => asset('storage/'.$path),
        ]);
    }

    public function imageDelete(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url'],
        ]);

        $path = str_replace(asset('storage').'/', '', $request->url);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            return response()->json(['message' => 'Image deleted successfully.']);
        }

        return response()->json(['message' => 'Image not found.'], 404);
    }
}
