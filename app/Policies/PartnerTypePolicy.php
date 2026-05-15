<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PartnerType;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_partner::types');
    }

    public function view(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('view_partner::types');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_partner::types');
    }

    public function update(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('update_partner::types');
    }

    public function delete(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('delete_partner::types');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_partner::types');
    }

    public function restore(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('restore_partner::types');
    }

    public function forceDelete(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('force_delete_partner::types');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_partner::types');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_partner::types');
    }

    public function replicate(AuthUser $authUser, PartnerType $partnerType): bool
    {
        return $authUser->can('replicate_partner::types');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_partner::types');
    }

}