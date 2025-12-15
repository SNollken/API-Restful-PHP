<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sale;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'status' => 'completed'
        ];
    }
}