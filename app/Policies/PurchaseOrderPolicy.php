<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('purchases.view') || $user->can('purchases.approve');
    }

    public function view(User $user, PurchaseOrder $po)
    {
        return $user->can('purchases.view') || $user->can('purchases.approve');
    }

    public function create(User $user)
    {
        return $user->can('purchases.create');
    }

    public function update(User $user, PurchaseOrder $po)
    {
        return $user->can('purchases.create') && $po->status === 'draft';
    }

    public function approve(User $user, PurchaseOrder $po)
    {
        return $user->can('purchases.approve');
    }
}
