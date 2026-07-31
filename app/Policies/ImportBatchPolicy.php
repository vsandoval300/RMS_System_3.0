<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ImportBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImportBatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_import::batch');
    }

    public function view(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('view_import::batch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_import::batch');
    }

    public function update(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('update_import::batch');
    }

    public function delete(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('delete_import::batch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_import::batch');
    }

    public function restore(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('restore_import::batch');
    }

    public function forceDelete(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('force_delete_import::batch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_import::batch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_import::batch');
    }

    public function replicate(AuthUser $authUser, ImportBatch $importBatch): bool
    {
        return $authUser->can('replicate_import::batch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_import::batch');
    }

}