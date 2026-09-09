<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Language;
use Illuminate\Auth\Access\AuthorizationException;

class LanguagePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(Admin $user, Language $language): bool
    {
        if ($language->is_default) {
            throw new AuthorizationException('Default language cannot be deleted');
        }

        if ($language->code === 'en') {
            throw new AuthorizationException('English language cannot be deleted');
        }

        return true;
    }
}
