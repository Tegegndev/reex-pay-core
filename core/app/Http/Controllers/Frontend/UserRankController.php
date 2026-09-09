<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserRank;

class UserRankController extends Controller
{
    public function showcase()
    {

        $userRanks = UserRank::where('is_active', 1)->get();

        return view('frontend.user.rank.showcase', compact('userRanks'));
    }
}
