<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('hr.view')
            || $user->hasAnyRole(['gm', 'hr_manager', 'hr_officer', 'admin', 'global_admin']);
    }

    public function view(User $user, Employee $e)
    {
        return $user->can('hr.view')
            || $user->hasAnyRole(['gm', 'hr_manager', 'hr_officer', 'admin', 'global_admin']);
    }

    public function create(User $user)
    {
        return $user->can('hr.manage')
            || $user->hasAnyRole(['hr_manager', 'hr_officer', 'admin', 'global_admin']);
    }

    public function update(User $user, Employee $e)
    {
        return $user->can('hr.manage')
            || $user->hasAnyRole(['hr_manager', 'hr_officer', 'admin', 'global_admin']);
    }

    public function delete(User $user, Employee $e)
    {
        return $user->can('hr.manage')
            || $user->hasAnyRole(['admin', 'global_admin']);
    }
}
