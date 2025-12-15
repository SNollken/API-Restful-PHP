<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sale;

class SalePolicy
{
    public function cancel(User $user, Sale $sale)
    {
        return $user->tokenCan('manage-sales');
    }
}