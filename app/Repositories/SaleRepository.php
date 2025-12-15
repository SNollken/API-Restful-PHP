<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function getAll()
    {
        return Sale::with('saleItems.product')->paginate(10);
    }

    public function create(array $data)
    {
        $sale = Sale::create([
            'total_amount' => $data['total_amount'],
            'status' => 'completed'
        ]);
        
        foreach ($data['items'] as $item) {
            $sale->saleItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ]);
        }
        
        return $sale;
    }

    public function find($id)
    {
        return Sale::with('saleItems.product')->find($id);
    }

    public function delete($id)
    {
        $sale = Sale::find($id);
        if ($sale) {
            return $sale->delete();
        }
        return false;
    }
}