<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Bank;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_banks');
    }

    public function view(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('view_banks');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_banks');
    }

    public function update(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('update_banks');
    }

    public function delete(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('delete_banks');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_banks');
    }

    public function restore(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('restore_banks');
    }

    public function forceDelete(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('force_delete_banks');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_banks');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_banks');
    }

    public function replicate(AuthUser $authUser, Bank $bank): bool
    {
        return $authUser->can('replicate_banks');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_banks');
    }

}