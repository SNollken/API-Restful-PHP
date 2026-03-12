<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->tokenCan('manage-products');
    }

    public function view(User $user, Product $product)
    {
        return $user->tokenCan('manage-products');
    }

    public function create(User $user)
    {
        return $user->tokenCan('manage-products');
    }

    public function update(User $user, Product $product)
    {
        return $user->tokenCan('manage-products');
    }

    public function delete(User $user, Product $product)
    {
        return $user->tokenCan('manage-products');
    }
}
