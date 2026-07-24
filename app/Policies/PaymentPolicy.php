<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->can('finance.view'); }
    public function view(User $user, Payment $p) { return $user->can('finance.view'); }
    public function create(User $user) { return $user->can('finance.manage'); }
    public function update(User $user, Payment $p) { return $user->can('finance.manage'); }
}
