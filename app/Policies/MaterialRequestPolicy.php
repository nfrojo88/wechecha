<?php

namespace App\Policies;

use App\Models\MaterialRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('material_requests.view') || $user->hasAnyRole(['coordinator', 'Coordinator']);
    }

    public function view(User $user, MaterialRequest $mr)
    {
        // If user is restricted to a store, they can only view requests destined for their store
        if ($user->store_id && $user->store_id !== $mr->destination_store_id) {
            return false;
        }
        return $user->can('material_requests.view') || $user->hasAnyRole(['coordinator', 'Coordinator']);
    }

    public function create(User $user)
    {
        return $user->can('material_requests.create') || $user->hasAnyRole(['coordinator', 'Coordinator']);
    }

    public function update(User $user, MaterialRequest $mr)
    {
        if ($user->store_id && $user->store_id !== $mr->destination_store_id) {
            return false;
        }
        return ($user->can('material_requests.create') || $user->hasAnyRole(['coordinator', 'Coordinator'])) && $mr->status === 'draft';
    }

    public function approve(User $user, MaterialRequest $mr)
    {
        return $user->can('material_requests.approve') || $user->hasAnyRole(['store_manager', 'Store Manager']);
    }
}
