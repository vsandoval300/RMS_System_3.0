<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Producer;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProducerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_producers');
    }

    public function view(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('view_producers');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_producers');
    }

    public function update(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('update_producers');
    }

    public function delete(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('delete_producers');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_producers');
    }

    public function restore(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('restore_producers');
    }

    public function forceDelete(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('force_delete_producers');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_producers');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_producers');
    }

    public function replicate(AuthUser $authUser, Producer $producer): bool
    {
        return $authUser->can('replicate_producers');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_producers');
    }

}