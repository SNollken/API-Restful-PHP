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
        $summary = $this->saleRepository->getSalesSummary();

        return [
            'total_sales' => (int) $summary->total_sales,
            'total_revenue' => (float) $summary->total_revenue,
            'average_sale_value' => (float) $summary->average_sale_value,
        ];
    }
}
