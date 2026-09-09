<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = LoginActivity::with('user')
            ->latest()
            ->filter($request) // Applying the filter scope from the model
            ->paginate(10)->withQueryString();

        return view('backend.activity.index', compact('activities'));
    }
}
