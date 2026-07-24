<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole(['planning_manager', 'admin', 'global_admin'])) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('schedule.view') || $user->hasAnyRole(['Coordinator', 'coordinator', 'planning', 'site_engineer', 'technical_manager', 'admin', 'global_admin']);
    }

    public function view(User $user, Schedule $schedule)
    {
        if ($user->hasRole('planning') && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            if (!$user->projects->contains($schedule->project_id)) {
                return false;
            }
        }
        return $user->can('schedule.view') || $user->hasAnyRole(['Coordinator', 'coordinator', 'planning', 'site_engineer', 'technical_manager', 'admin', 'global_admin']);
    }

    public function create(User $user)
    {
        return $user->can('schedule.create');
    }

    public function update(User $user, Schedule $schedule)
    {
        if ($user->hasRole('planning') && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            if (!$user->projects->contains($schedule->project_id)) {
                return false;
            }
        }
        return $user->can('schedule.edit');
    }

    public function delete(User $user, Schedule $schedule)
    {
        if ($user->hasRole('planning') && !$user->hasAnyRole(['planning_manager', 'admin', 'global_admin', 'coordinator', 'Coordinator'])) {
            if (!$user->projects->contains($schedule->project_id)) {
                return false;
            }
        }
        return $user->can('schedule.delete');
    }
}
