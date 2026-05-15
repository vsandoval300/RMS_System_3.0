<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Country;
use Illuminate\Auth\Access\HandlesAuthorization;

class CountryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_countries');
    }

    public function view(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('view_countries');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_countries');
    }

    public function update(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('update_countries');
    }

    public function delete(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('delete_countries');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_countries');
    }

    public function restore(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('restore_countries');
    }

    public function forceDelete(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('force_delete_countries');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_countries');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_countries');
    }

    public function replicate(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('replicate_countries');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_countries');
    }

}