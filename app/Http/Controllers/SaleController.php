<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Services\SaleService;
use App\Http\Resources\SaleResource;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index()
    {
        $sales = $this->saleService->getAllSales();
        return SaleResource::collection($sales);
    }

    public function store(StoreSaleRequest $request)
    {
        $sale = $this->saleService->createSale($request->validated());
        return response()->json([
            "message" => "Sale created successfully!",
            "data" => new SaleResource($sale)
        ], 201);
    }

    public function show($id)
    {
        $sale = $this->saleService->getSale($id);
        if ($sale) {
            return new SaleResource($sale);
        } else {
            return response()->json(["message" => "Sale not found"], 404);
        }
    }

    public function destroy($id)
    {
        $sale = $this->saleService->getSale($id);
        if ($sale) {
            $this->saleService->cancelSale($id);
            return response()->json(["message" => "Sale cancelled successfully!"], 202);
        } else {
            return response()->json(["message" => "Sale not found"], 404);
        }
    }
}
