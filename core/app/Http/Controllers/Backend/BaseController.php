<?php

namespace App\Http\Controllers\Backend;

use App\Contracts\DefinesPermissions;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

abstract class BaseController extends Controller implements DefinesPermissions, HasMiddleware
{
    public static function middleware(): array
    {
        return collect(static::permissions())
            ->flatMap(function ($perm, $actions) {
                return collect(explode('|', $actions))->map(
                    fn ($action) => new Middleware("permission:{$perm}", only: [$action])
                );
            })
            ->values()
            ->toArray();
    }
}
