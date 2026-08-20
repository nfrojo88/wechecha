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
        return true;
    }

    public function view(User $user, MaterialRequest $mr)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['site_engineer', 'Site Engineer', 'admin', 'global_admin'])
            || $user->can('material_requests.create');
    }

    public function update(User $user, MaterialRequest $mr)
    {
        return ($user->id === $mr->created_by || $user->hasAnyRole(['site_engineer', 'Site Engineer', 'admin', 'global_admin']))
            && in_array($mr->status, ['draft', 'pending_planning']);
    }

    public function approvePlanning(User $user, MaterialRequest $mr)
    {
        return $user->hasAnyRole(['planning_manager', 'Planning Manager', 'planning_team', 'admin', 'global_admin'])
            || $user->can('material_requests.planning_approve');
    }

    public function dispatchCoordinator(User $user, MaterialRequest $mr)
    {
        return $user->hasAnyRole(['coordinator', 'Coordinator', 'admin', 'global_admin'])
            || $user->can('material_requests.coordinator_dispatch');
    }

    public function actionStoreManager(User $user, MaterialRequest $mr)
    {
        return $user->hasAnyRole(['store_manager', 'Store Manager', 'admin', 'global_admin'])
            || $user->can('material_requests.approve');
    }

    public function approve(User $user, MaterialRequest $mr)
    {
        return $user->hasAnyRole(['store_manager', 'Store Manager', 'coordinator', 'Coordinator', 'admin', 'global_admin'])
            || $user->can('material_requests.approve');
    }
}
