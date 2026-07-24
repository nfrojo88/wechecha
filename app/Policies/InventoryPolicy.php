<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole(['global_admin', 'admin'])) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('inventory.view') || $user->hasAnyRole(['Coordinator', 'coordinator', 'admin', 'global_admin']);
    }

    public function view(User $user, Inventory $inventory)
    {
        if ($user->hasAnyRole(['global_admin', 'admin', 'coordinator', 'Coordinator'])) {
            return true;
        }

        if ($user->store_id && $user->store_id !== $inventory->store_id) {
            return false;
        }

        return $user->can('inventory.view');
    }

    public function create(User $user)
    {
        return $user->can('inventory.create');
    }

    public function update(User $user, Inventory $inventory)
    {
        if ($user->hasAnyRole(['global_admin', 'admin'])) {
            return true;
        }

        if ($user->store_id && $user->store_id !== $inventory->store_id) {
            return false;
        }

        return $user->can('inventory.edit');
    }

    public function delete(User $user, Inventory $inventory)
    {
        return $user->can('inventory.delete');
    }
}
