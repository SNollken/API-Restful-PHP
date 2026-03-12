<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    protected int $productId;
    protected int $available;
    protected int $requested;

    public function __construct(int $productId, int $available, int $requested)
    {
        parent::__construct("Insufficient stock for product: {$productId}");
        $this->productId = $productId;
        $this->available = $available;
        $this->requested = $requested;
    }

    public function context(): array
    {
        return [
            'product_id' => $this->productId,
            'available' => $this->available,
            'requested' => $this->requested,
        ];
    }
}
