<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CustomLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CustomLandingController extends Controller
{
    public function index()
    {
        $landings = CustomLanding::latest()->get();

        return view('backend.landings.index', compact('landings'));
    }

    public function create()
    {
        return view('backend.landings.create');
    }

    private function extractAndReplaceFolder($zipFile, $path, $folder)
    {
        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($path);
            $zip->close();

            // Replace {folder} placeholder in index.html
            $indexPath = $path.'/index.html';
            if (File::exists($indexPath)) {
                $content = File::get($indexPath);
                $content = str_replace('{folder}', $folder, $content);
                File::put($indexPath, $content);
            }
        } else {
            notifyEvs('error', 'Failed to extract ZIP file');

            return false;
        }

        return true;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|unique:custom_landings,name',
            'zipFile' => 'required|file|mimes:zip|max:10240',
        ]);

        $folder = Str::slug($request->name).'-'.time();
        $path   = public_path("custom-landings/{$folder}");
        File::makeDirectory($path, 0755, true);

        if (! $this->extractAndReplaceFolder($request->file('zipFile'), $path, $folder)) {
            return redirect()->back();
        }

        // Disable all other landings
        CustomLanding::query()->update(['status' => false]);

        // Create the new landing page
        CustomLanding::create([
            'name'   => $request->name,
            'folder' => $folder,
            'status' => true,
        ]);

        notifyEvs('success', 'Landing uploaded and extracted successfully');

        return redirect()->route('admin.custom-landing.index');
    }

    public function edit($landing_page)
    {
        $landing_page = CustomLanding::find($landing_page);

        return view('backend.landings.edit', compact('landing_page'));
    }

    public function update(Request $request, $landing_page)
    {
        $landing_page = CustomLanding::find($landing_page);
        $request->validate([
            'name'    => 'required|string|unique:custom_landings,name,'.$landing_page->id,
            'status'  => 'required|boolean',
            'zipFile' => 'nullable|file|mimes:zip|max:10240',
        ]);

        if ($request->status) {
            CustomLanding::where('id', '!=', $landing_page->id)->update(['status' => false]);
        }

        if ($request->hasFile('zipFile')) {
            $folder = $landing_page->folder;
            $path   = public_path("custom-landings/{$folder}");

            // Clear existing files
            File::cleanDirectory($path);

            if (! $this->extractAndReplaceFolder($request->file('zipFile'), $path, $folder)) {
                return redirect()->back();
            }
        }

        $landing_page->update([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        notifyEvs('success', 'Landing updated successfully');

        return redirect()->route('admin.custom-landing.index');
    }

    public function manageHtml($id)
    {
        $landing_page = CustomLanding::find($id);
        $indexPath    = public_path("custom-landings/{$landing_page->folder}/index.html");
        $content      = File::exists($indexPath) ? File::get($indexPath) : '';

        return view('backend.landings.manage_html', compact('landing_page', 'content'));
    }

    public function manageHtmlUpdate(Request $request, $id)
    {
        $landing_page = CustomLanding::find($id);
        $indexPath    = public_path("custom-landings/{$landing_page->folder}/index.html");

        $htmlContent = $request->input('htmlContent');

        File::put($indexPath, $htmlContent);

        notifyEvs('success', 'HTML updated successfully');

        return redirect()->back();
    }

    public function destroy($landing_page)
    {
        $landing_page = CustomLanding::find($landing_page);
        // Delete only the specific landing page folder
        File::deleteDirectory(public_path("custom-landings/{$landing_page->folder}"));
        $landing_page->delete();

        notifyEvs('success', 'Landing deleted successfully');

        return redirect()->route('admin.custom-landing.index');
    }
}
