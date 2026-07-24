<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasAnyRole(['admin', 'global_admin', 'store_manager'])) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('products.view');
    }

    public function view(User $user, Product $product)
    {
        return $user->hasPermissionTo('products.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('products.create');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasPermissionTo('products.edit');
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasPermissionTo('products.delete');
    }
}
