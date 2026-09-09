<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiDocsController extends Controller
{
    public function index()
    {
        $isBreadcrumb = true;

        return view('general.api-docs.index', compact('isBreadcrumb'));
    }
}
