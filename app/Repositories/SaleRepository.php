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
        
        return $sale->load('saleItems.product');
    }

    public function find($id)
    {
        return Sale::with('saleItems.product')->find($id);
    }

    public function cancel(Sale $sale)
    {
        $sale->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return $sale->fresh('saleItems.product');
    }

    public function getSalesSummary()
    {
        return Sale::where('status', 'completed')
            ->selectRaw(
                'COUNT(*) as total_sales, ' .
                'COALESCE(SUM(total_amount), 0) as total_revenue, ' .
                'COALESCE(AVG(total_amount), 0) as average_sale_value'
            )
            ->first();
    }
}
