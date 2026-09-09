<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function download($filePath)
    {
        // Resolve the full file path
        $fullPath = storage_path('app/public/'.$filePath);

        // Check if the file exists
        if (! file_exists($fullPath)) {
            abort(404, 'File not found.');
        }

        // Serve the file for download
        return response()->download($fullPath);
    }
}
