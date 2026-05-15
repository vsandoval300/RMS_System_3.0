<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Holding;
use Illuminate\Auth\Access\HandlesAuthorization;

class HoldingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_holding');
    }

    public function view(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('view_holding');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_holding');
    }

    public function update(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('update_holding');
    }

    public function delete(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('delete_holding');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_holding');
    }

    public function restore(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('restore_holding');
    }

    public function forceDelete(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('force_delete_holding');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_holding');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_holding');
    }

    public function replicate(AuthUser $authUser, Holding $holding): bool
    {
        return $authUser->can('replicate_holding');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_holding');
    }

}