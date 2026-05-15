<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_regions');
    }

    public function view(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('view_regions');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_regions');
    }

    public function update(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('update_regions');
    }

    public function delete(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('delete_regions');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_regions');
    }

    public function restore(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('restore_regions');
    }

    public function forceDelete(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('force_delete_regions');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_regions');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_regions');
    }

    public function replicate(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('replicate_regions');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_regions');
    }

}