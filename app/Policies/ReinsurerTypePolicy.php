<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ReinsurerType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReinsurerTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_reinsurer::type');
    }

    public function view(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('view_reinsurer::type');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_reinsurer::type');
    }

    public function update(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('update_reinsurer::type');
    }

    public function delete(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('delete_reinsurer::type');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_reinsurer::type');
    }

    public function restore(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('restore_reinsurer::type');
    }

    public function forceDelete(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('force_delete_reinsurer::type');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_reinsurer::type');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_reinsurer::type');
    }

    public function replicate(AuthUser $authUser, ReinsurerType $reinsurerType): bool
    {
        return $authUser->can('replicate_reinsurer::type');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_reinsurer::type');
    }

}