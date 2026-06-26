<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CostScheme;
use Illuminate\Auth\Access\HandlesAuthorization;

class CostSchemePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_cost::scheme');
    }

    public function view(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('view_cost::scheme');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_cost::scheme');
    }

    public function update(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('update_cost::scheme');
    }

    public function delete(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('delete_cost::scheme');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_cost::scheme');
    }

    public function restore(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('restore_cost::scheme');
    }

    public function forceDelete(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('force_delete_cost::scheme');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_cost::scheme');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_cost::scheme');
    }

    public function replicate(AuthUser $authUser, CostScheme $costScheme): bool
    {
        return $authUser->can('replicate_cost::scheme');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_cost::scheme');
    }

}