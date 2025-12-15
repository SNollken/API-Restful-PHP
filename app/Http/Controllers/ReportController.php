<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function salesSummary()
    {
        $summary = $this->reportService->getSalesSummary();
        return response()->json([
            "message" => "Sales summary retrieved successfully!",
            "data" => $summary
        ], 200);
    }
}