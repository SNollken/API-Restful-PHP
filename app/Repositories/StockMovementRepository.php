<?php

namespace App\Repositories;

use App\Models\StockMovement;

class StockMovementRepository
{
    public function create(array $data)
    {
        return StockMovement::create($data);
    }
}