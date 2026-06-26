<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Coverage;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoveragePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_coverages');
    }

    public function view(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('view_coverages');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_coverages');
    }

    public function update(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('update_coverages');
    }

    public function delete(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('delete_coverages');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_coverages');
    }

    public function restore(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('restore_coverages');
    }

    public function forceDelete(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('force_delete_coverages');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_coverages');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_coverages');
    }

    public function replicate(AuthUser $authUser, Coverage $coverage): bool
    {
        return $authUser->can('replicate_coverages');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_coverages');
    }

}