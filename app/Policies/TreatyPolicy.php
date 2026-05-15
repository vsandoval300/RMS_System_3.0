<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Treaty;
use Illuminate\Auth\Access\HandlesAuthorization;

class TreatyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_treaty');
    }

    public function view(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('view_treaty');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_treaty');
    }

    public function update(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('update_treaty');
    }

    public function delete(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('delete_treaty');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_treaty');
    }

    public function restore(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('restore_treaty');
    }

    public function forceDelete(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('force_delete_treaty');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_treaty');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_treaty');
    }

    public function replicate(AuthUser $authUser, Treaty $treaty): bool
    {
        return $authUser->can('replicate_treaty');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_treaty');
    }

}