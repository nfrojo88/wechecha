<?php

namespace App\Policies;

use App\Models\EngWorkOrder;
use App\Models\User;

class EngWorkOrderPolicy
{
    /**
     * Anyone authenticated can view the schedule list / calendar.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['eng_schedule.view', 'eng_schedule.manage', 'eng_schedule.create', 'eng_schedule.assign'])
            || $user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning', 'technical_manager', 'site_engineer']);
    }

    /**
     * Engineers can see their own. Planners see all.
     */
    public function view(User $user, EngWorkOrder $workOrder): bool
    {
        if ($user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning', 'technical_manager'])) {
            return true;
        }
        return $workOrder->isAssignedTo($user->id);
    }

    /**
     * Only planners and admins can create work orders.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning', 'technical_manager'])
            || $user->hasAnyPermission(['eng_schedule.create', 'eng_schedule.manage']);
    }

    /**
     * Only planners / admins can edit work orders.
     */
    public function update(User $user, EngWorkOrder $workOrder): bool
    {
        return $user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning'])
            || $user->hasPermissionTo('eng_schedule.manage');
    }

    /**
     * Only planners / admins can delete / cancel.
     */
    public function delete(User $user, EngWorkOrder $workOrder): bool
    {
        return $user->hasAnyRole(['admin', 'global_admin', 'planning_manager'])
            || $user->hasPermissionTo('eng_schedule.manage');
    }

    /**
     * Engineers can update status ONLY on their own tasks.
     * Planners can update status on any.
     */
    public function updateStatus(User $user, EngWorkOrder $workOrder): bool
    {
        if ($user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning'])) {
            return true;
        }
        return $workOrder->isAssignedTo($user->id);
    }

    /**
     * Assigned engineers or planners can comment.
     */
    public function comment(User $user, EngWorkOrder $workOrder): bool
    {
        if ($user->hasAnyRole(['admin', 'global_admin', 'planning_manager', 'planning'])) {
            return true;
        }
        return $workOrder->isAssignedTo($user->id);
    }
}
