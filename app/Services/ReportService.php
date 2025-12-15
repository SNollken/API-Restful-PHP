<?php

namespace App\Services;

use App\Repositories\SaleRepository;

class ReportService
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    public function getSalesSummary()
    {
        $sales = $this->saleRepository->getAll();
        return [
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'average_sale_value' => $sales->avg('total_amount')
        ];
    }
}