<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UnderwrittenBudget;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnderwrittenBudgetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_underwritten::budget');
    }

    public function view(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('view_underwritten::budget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_underwritten::budget');
    }

    public function update(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('update_underwritten::budget');
    }

    public function delete(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('delete_underwritten::budget');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_underwritten::budget');
    }

    public function restore(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('restore_underwritten::budget');
    }

    public function forceDelete(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('force_delete_underwritten::budget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_underwritten::budget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_underwritten::budget');
    }

    public function replicate(AuthUser $authUser, UnderwrittenBudget $underwrittenBudget): bool
    {
        return $authUser->can('replicate_underwritten::budget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_underwritten::budget');
    }

}