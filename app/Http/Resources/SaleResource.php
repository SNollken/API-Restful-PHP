<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'items' => $this->whenLoaded('saleItems', function () {
                return $this->saleItems->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }
}
