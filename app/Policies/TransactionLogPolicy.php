<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TransactionLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_transaction::log');
    }

    public function view(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('view_transaction::log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_transaction::log');
    }

    public function update(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('update_transaction::log');
    }

    public function delete(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('delete_transaction::log');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_transaction::log');
    }

    public function restore(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('restore_transaction::log');
    }

    public function forceDelete(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('force_delete_transaction::log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_transaction::log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_transaction::log');
    }

    public function replicate(AuthUser $authUser, TransactionLog $transactionLog): bool
    {
        return $authUser->can('replicate_transaction::log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_transaction::log');
    }

}