<?php

namespace App\Policies;

use App\Models\ExpenseRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Global admins and system admins have full bypass.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole(['global_admin', 'admin'])) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any expense requests.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtered at query level per role
    }

    /**
     * Determine whether the user can view the specific expense request.
     * Strict check for direct URL access (e.g., /expense-requests/42).
     */
    public function view(User $user, ExpenseRequest $expenseRequest): bool
    {
        $roleNames = strtolower(implode(' ', $user->getRoleNames()->toArray()));

        // Finance Head has unrestricted visibility across all requests
        if ($user->hasAnyRole(['finance_head', 'finance_manager']) || str_contains($roleNames, 'finance_head') || str_contains($roleNames, 'finance_manager')) {
            return true;
        }

        // Submitter can always view their own request
        if ($expenseRequest->user_id === $user->id) {
            return true;
        }

        // HR Reviewer
        $isHr = $user->can('hr.view') || str_contains($roleNames, 'hr');
        if ($isHr) {
            if ($expenseRequest->status === 'Pending (HR Review)' || $expenseRequest->hr_reviewer_id === $user->id) {
                return true;
            }
        }

        // GM Approver
        $isGm = str_contains($roleNames, 'gm') || $user->hasRole('gm');
        if ($isGm) {
            if ($expenseRequest->status === 'Pending (GM Review)' || $expenseRequest->gm_approver_id === $user->id || $expenseRequest->gm_reviewer_id === $user->id) {
                return true;
            }
        }

        // Finance Staff / Cashier
        $isFinanceStaff = str_contains($roleNames, 'finance') || str_contains($roleNames, 'cashier') || str_contains($roleNames, 'accountant');
        if ($isFinanceStaff) {
            if ($expenseRequest->assigned_finance_staff_id === $user->id || $expenseRequest->finance_staff_id === $user->id || $expenseRequest->paid_by === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create an expense request.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated employee can submit
    }

    /**
     * Determine whether the user can perform HR Review (Approve/Reject).
     */
    public function hrReview(User $user, ExpenseRequest $expenseRequest): bool
    {
        $roleNames = strtolower(implode(' ', $user->getRoleNames()->toArray()));
        $isHr = $user->can('hr.view') || str_contains($roleNames, 'hr');

        return $isHr && $expenseRequest->status === 'Pending (HR Review)';
    }

    /**
     * Determine whether the user can perform GM Review (> 5000 ETB).
     */
    public function gmReview(User $user, ExpenseRequest $expenseRequest): bool
    {
        $roleNames = strtolower(implode(' ', $user->getRoleNames()->toArray()));
        $isGm = str_contains($roleNames, 'gm') || $user->hasRole('gm');

        return $isGm && $expenseRequest->status === 'Pending (GM Review)';
    }

    /**
     * Determine whether the user can perform Finance Assignment.
     */
    public function financeAssign(User $user, ExpenseRequest $expenseRequest): bool
    {
        $roleNames = strtolower(implode(' ', $user->getRoleNames()->toArray()));
        $isFinanceHead = $user->hasAnyRole(['finance_head', 'finance_manager']) || str_contains($roleNames, 'finance_head') || str_contains($roleNames, 'finance_manager');

        return $isFinanceHead && in_array($expenseRequest->status, ['Approved - Assigned to Finance', 'Assigned to Finance']);
    }

    /**
     * Determine whether the user can mark payment as Paid.
     */
    public function markPaid(User $user, ExpenseRequest $expenseRequest): bool
    {
        $roleNames = strtolower(implode(' ', $user->getRoleNames()->toArray()));
        $isFinanceHead = $user->hasAnyRole(['finance_head', 'finance_manager']) || str_contains($roleNames, 'finance_head') || str_contains($roleNames, 'finance_manager');
        $isAssignedStaff = ($expenseRequest->assigned_finance_staff_id === $user->id || $expenseRequest->finance_staff_id === $user->id);

        return ($isFinanceHead || $isAssignedStaff) && $expenseRequest->status === 'Assigned to Finance';
    }
}
