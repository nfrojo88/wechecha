<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->can('finance.view') || $user->can('finance.approve'); }
    public function view(User $user, Expense $e) { return $user->can('finance.view') || $user->can('finance.approve'); }
    public function create(User $user) { return $user->can('finance.view') || $user->can('finance.approve'); }
    public function update(User $user, Expense $e) { return $user->can('finance.view') && $e->status === 'pending'; }
    public function approve(User $user, Expense $e) { return $user->can('finance.approve'); }
}
