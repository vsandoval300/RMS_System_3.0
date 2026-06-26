<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Partner;
use Illuminate\Auth\Access\HandlesAuthorization;

class PartnerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_partners');
    }

    public function view(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('view_partners');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_partners');
    }

    public function update(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('update_partners');
    }

    public function delete(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('delete_partners');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_partners');
    }

    public function restore(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('restore_partners');
    }

    public function forceDelete(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('force_delete_partners');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_partners');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_partners');
    }

    public function replicate(AuthUser $authUser, Partner $partner): bool
    {
        return $authUser->can('replicate_partners');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_partners');
    }

}