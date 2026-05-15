<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Currency;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_currencies');
    }

    public function view(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('view_currencies');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_currencies');
    }

    public function update(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('update_currencies');
    }

    public function delete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('delete_currencies');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_currencies');
    }

    public function restore(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('restore_currencies');
    }

    public function forceDelete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('force_delete_currencies');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_currencies');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_currencies');
    }

    public function replicate(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('replicate_currencies');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_currencies');
    }

}