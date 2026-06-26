<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Manager;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManagerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_manager');
    }

    public function view(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('view_manager');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_manager');
    }

    public function update(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('update_manager');
    }

    public function delete(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('delete_manager');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_manager');
    }

    public function restore(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('restore_manager');
    }

    public function forceDelete(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('force_delete_manager');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_manager');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_manager');
    }

    public function replicate(AuthUser $authUser, Manager $manager): bool
    {
        return $authUser->can('replicate_manager');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_manager');
    }

}