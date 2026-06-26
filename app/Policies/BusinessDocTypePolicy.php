<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BusinessDocType;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessDocTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_business::doc::types');
    }

    public function view(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('view_business::doc::types');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_business::doc::types');
    }

    public function update(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('update_business::doc::types');
    }

    public function delete(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('delete_business::doc::types');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_business::doc::types');
    }

    public function restore(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('restore_business::doc::types');
    }

    public function forceDelete(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('force_delete_business::doc::types');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_business::doc::types');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_business::doc::types');
    }

    public function replicate(AuthUser $authUser, BusinessDocType $businessDocType): bool
    {
        return $authUser->can('replicate_business::doc::types');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_business::doc::types');
    }

}