<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->can('hr.view') || $user->hasRole('Finance head') || $user->hasRole('finance_head') || $user->hasRole('admin') || $user->hasRole('global_admin'); }
    public function view(User $user, Payroll $p) { return $user->can('hr.view') || $user->hasRole('Finance head') || $user->hasRole('finance_head') || $user->hasRole('admin') || $user->hasRole('global_admin'); }
    public function create(User $user) { return $user->can('hr.manage'); }
    public function update(User $user, Payroll $p) { return $user->can('hr.manage') && $p->status === 'draft'; }
}
