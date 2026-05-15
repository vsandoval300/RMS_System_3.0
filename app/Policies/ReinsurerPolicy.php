<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Reinsurer;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReinsurerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_reinsurers');
    }

    public function view(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('view_reinsurers');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_reinsurers');
    }

    public function update(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('update_reinsurers');
    }

    public function delete(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('delete_reinsurers');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_reinsurers');
    }

    public function restore(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('restore_reinsurers');
    }

    public function forceDelete(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('force_delete_reinsurers');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_reinsurers');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_reinsurers');
    }

    public function replicate(AuthUser $authUser, Reinsurer $reinsurer): bool
    {
        return $authUser->can('replicate_reinsurers');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_reinsurers');
    }

}