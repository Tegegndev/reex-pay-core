<?php

namespace App\Http\Controllers\Backend;

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PluginController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index|pluginType|edit|update' => 'plugins-manage',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->getPluginsView('general');
    }

    public function pluginType($plugin_type)
    {
        return $this->getPluginsView($plugin_type);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plugin              = Plugin::find($id);
        $plugin->credentials = json_decode($plugin->credentials, true);

        return view('backend.settings.plugin.partials.__manage_append', compact('plugin'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'credentials' => 'required|array',
            'fields'      => 'nullable|array',
        ]);

        // Handle optional fields and encode them to JSON if present
        if (isset($validatedData['fields'])) {
            $validatedData['credentials']['fields'] = $validatedData['fields'];
        }

        // Encode the credentials array to JSON
        $credentials = json_encode($validatedData['credentials']);

        // Find and update the plugin
        $plugin = Plugin::findOrFail($id);
        $plugin->update([
            'credentials' => $credentials,
            'status'      => $request->input('status', 0), // Set status to 0 if not provided
        ]);

        if ($plugin->type == 'notification') {
            Artisan::call('config:cache');
        }

        // Notify success and redirect back
        notifyEvs('success', __('Info Updated Successfully'));

        return redirect()->back();
    }

    private function getPluginsView($pluginType)
    {
        $plugins = Plugin::where('type', $pluginType)->get();

        return view('backend.settings.plugin.index', compact('plugins'));
    }
}
