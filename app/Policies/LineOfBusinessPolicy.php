<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LineOfBusiness;
use Illuminate\Auth\Access\HandlesAuthorization;

class LineOfBusinessPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_line::of::business');
    }

    public function view(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('view_line::of::business');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_line::of::business');
    }

    public function update(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('update_line::of::business');
    }

    public function delete(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('delete_line::of::business');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_line::of::business');
    }

    public function restore(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('restore_line::of::business');
    }

    public function forceDelete(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('force_delete_line::of::business');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_line::of::business');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_line::of::business');
    }

    public function replicate(AuthUser $authUser, LineOfBusiness $lineOfBusiness): bool
    {
        return $authUser->can('replicate_line::of::business');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_line::of::business');
    }

}