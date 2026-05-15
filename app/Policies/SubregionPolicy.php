<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Subregion;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubregionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_subregions');
    }

    public function view(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('view_subregions');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_subregions');
    }

    public function update(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('update_subregions');
    }

    public function delete(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('delete_subregions');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_subregions');
    }

    public function restore(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('restore_subregions');
    }

    public function forceDelete(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('force_delete_subregions');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_subregions');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_subregions');
    }

    public function replicate(AuthUser $authUser, Subregion $subregion): bool
    {
        return $authUser->can('replicate_subregions');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_subregions');
    }

}