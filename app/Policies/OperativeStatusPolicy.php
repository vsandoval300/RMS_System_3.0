<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperativeStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperativeStatusPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_operative::statuses');
    }

    public function view(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('view_operative::statuses');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_operative::statuses');
    }

    public function update(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('update_operative::statuses');
    }

    public function delete(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('delete_operative::statuses');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_operative::statuses');
    }

    public function restore(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('restore_operative::statuses');
    }

    public function forceDelete(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('force_delete_operative::statuses');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_operative::statuses');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_operative::statuses');
    }

    public function replicate(AuthUser $authUser, OperativeStatus $operativeStatus): bool
    {
        return $authUser->can('replicate_operative::statuses');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_operative::statuses');
    }

}