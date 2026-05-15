<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Director;
use Illuminate\Auth\Access\HandlesAuthorization;

class DirectorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_director');
    }

    public function view(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('view_director');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_director');
    }

    public function update(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('update_director');
    }

    public function delete(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('delete_director');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_director');
    }

    public function restore(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('restore_director');
    }

    public function forceDelete(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('force_delete_director');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_director');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_director');
    }

    public function replicate(AuthUser $authUser, Director $director): bool
    {
        return $authUser->can('replicate_director');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_director');
    }

}