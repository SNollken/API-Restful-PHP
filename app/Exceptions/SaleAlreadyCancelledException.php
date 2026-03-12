<?php

namespace App\Exceptions;

use RuntimeException;

class SaleAlreadyCancelledException extends RuntimeException
{
    protected int $saleId;

    public function __construct(int $saleId)
    {
        parent::__construct("Sale {$saleId} is already cancelled.");
        $this->saleId = $saleId;
    }

    public function context(): array
    {
        return [
            'sale_id' => $this->saleId,
        ];
    }
}
