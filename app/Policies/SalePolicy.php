<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sale;

class SalePolicy
{
    public function viewAny(User $user)
    {
        return $user->tokenCan('manage-sales');
    }

    public function view(User $user, Sale $sale)
    {
        return $user->tokenCan('manage-sales');
    }

    public function create(User $user)
    {
        return $user->tokenCan('manage-sales');
    }

    public function delete(User $user, Sale $sale)
    {
        return $user->tokenCan('manage-sales');
    }
}
