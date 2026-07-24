<?php

namespace App\Policies;

use App\Models\Boq;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoqPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('boq.view');
    }

    public function view(User $user, Boq $boq)
    {
        return $user->can('boq.view');
    }

    public function create(User $user)
    {
        return $user->can('boq.create');
    }

    public function update(User $user, Boq $boq)
    {
        return $user->can('boq.edit');
    }

    public function delete(User $user, Boq $boq)
    {
        return $user->can('boq.delete');
    }
    
    public function approve(User $user, Boq $boq)
    {
        return $user->can('boq.approve');
    }
}
