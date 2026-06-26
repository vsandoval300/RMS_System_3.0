<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BankAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_bank::accounts');
    }

    public function view(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('view_bank::accounts');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_bank::accounts');
    }

    public function update(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('update_bank::accounts');
    }

    public function delete(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('delete_bank::accounts');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_bank::accounts');
    }

    public function restore(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('restore_bank::accounts');
    }

    public function forceDelete(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('force_delete_bank::accounts');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_bank::accounts');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_bank::accounts');
    }

    public function replicate(AuthUser $authUser, BankAccount $bankAccount): bool
    {
        return $authUser->can('replicate_bank::accounts');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_bank::accounts');
    }

}