<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ApprovalStatus;
use App\Models\Business;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BusinessPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_business');
    }

    public function view(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('view_business');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_business');
    }

    public function update(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('update_business');
    }

    public function delete(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('delete_business');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_business');
    }

    public function restore(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('restore_business');
    }

    public function forceDelete(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('force_delete_business');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_business');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_business');
    }

    public function replicate(AuthUser $authUser, Business $business): bool
    {
        return $authUser->can('replicate_business');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_business');
    }

    // ── Approval workflow ──────────────────────────────────────────────────────

    // Creator submits their own business when it is Draft or Rejected
    public function submitForReview(AuthUser $authUser, Business $business): bool
    {
        return $authUser->id == $business->created_by_user
            && in_array($business->approval_status, [ApprovalStatus::DRAFT, ApprovalStatus::REJECTED]);
    }

    // Direct manager of the creator approves when Pending
    public function approveBusiness(AuthUser $authUser, Business $business): bool
    {
        return $authUser->id == $business->createdByUser?->manager_id
            && $business->approval_status === ApprovalStatus::PENDING;
    }

    // Direct manager of the creator requests revision when Pending
    public function requestRevision(AuthUser $authUser, Business $business): bool
    {
        return $authUser->id == $business->createdByUser?->manager_id
            && $business->approval_status === ApprovalStatus::PENDING;
    }

}