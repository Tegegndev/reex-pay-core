<?php

namespace App\Policies;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerchantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any merchants.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the merchant.
     */
    public function view(User $user, Merchant $merchant): bool
    {
        return $user->id === $merchant->user_id;
    }

    /**
     * Determine whether the user can create merchants.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the merchant.
     */
    public function update(User $user, Merchant $merchant): bool
    {
        return $user->id === $merchant->user_id;
    }

    /**
     * Determine whether the user can delete the merchant.
     */
    public function delete(User $user, Merchant $merchant): bool
    {
        return $user->id === $merchant->user_id;
    }

    /**
     * Determine whether the user can restore the merchant.
     */
    public function restore(User $user, Merchant $merchant): bool
    {
        return $user->id === $merchant->user_id;
    }

    /**
     * Determine whether the user can permanently delete the merchant.
     */
    public function forceDelete(User $user, Merchant $merchant): bool
    {
        return $user->id === $merchant->user_id;
    }
}
