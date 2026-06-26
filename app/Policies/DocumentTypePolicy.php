<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DocumentType;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_corporate::documents');
    }

    public function view(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('view_corporate::documents');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_corporate::documents');
    }

    public function update(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('update_corporate::documents');
    }

    public function delete(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('delete_corporate::documents');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_corporate::documents');
    }

    public function restore(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('restore_corporate::documents');
    }

    public function forceDelete(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('force_delete_corporate::documents');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_corporate::documents');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_corporate::documents');
    }

    public function replicate(AuthUser $authUser, DocumentType $documentType): bool
    {
        return $authUser->can('replicate_corporate::documents');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_corporate::documents');
    }

}