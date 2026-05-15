<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Industry;
use Illuminate\Auth\Access\HandlesAuthorization;

class IndustryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_industry');
    }

    public function view(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('view_industry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_industry');
    }

    public function update(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('update_industry');
    }

    public function delete(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('delete_industry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_industry');
    }

    public function restore(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('restore_industry');
    }

    public function forceDelete(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('force_delete_industry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_industry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_industry');
    }

    public function replicate(AuthUser $authUser, Industry $industry): bool
    {
        return $authUser->can('replicate_industry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_industry');
    }

}