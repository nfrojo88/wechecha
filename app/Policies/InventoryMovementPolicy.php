<?php

namespace App\Policies;

use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryMovementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, InventoryMovement $movement)
    {
        if ($user->hasRole('global_admin') || $user->hasRole('admin')) {
            return true;
        }

        if ($user->store_id && $user->store_id !== $movement->inventory->store_id) {
            return false;
        }

        return $user->hasPermissionTo('inventory.view');
    }
}
