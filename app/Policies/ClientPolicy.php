<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_clients');
    }

    public function view(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('view_clients');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_clients');
    }

    public function update(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('update_clients');
    }

    public function delete(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('delete_clients');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_clients');
    }

    public function restore(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('restore_clients');
    }

    public function forceDelete(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('force_delete_clients');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_clients');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_clients');
    }

    public function replicate(AuthUser $authUser, Client $client): bool
    {
        return $authUser->can('replicate_clients');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_clients');
    }

}