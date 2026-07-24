<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeContract;

class EmployeeContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'hr_officer', 'finance_manager', 'admin']);
    }

    public function view(User $user, EmployeeContract $contract): bool
    {
        return $user->hasRole(['hr_manager', 'hr_officer', 'finance_manager', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'admin']);
    }

    public function update(User $user, EmployeeContract $contract): bool
    {
        return $user->hasRole(['hr_manager', 'admin']) && in_array($contract->status, ['draft', 'approved']);
    }

    public function approve(User $user): bool
    {
        return $user->hasRole(['hr_manager', 'finance_manager', 'admin']);
    }
}
