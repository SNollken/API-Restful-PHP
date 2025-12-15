<?php

namespace App\Services;

use App\Repositories\SaleRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StockMovementRepository;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected $saleRepository;
    protected $productRepository;
    protected $stockMovementRepository;

    public function __construct(
        SaleRepository $saleRepository,
        ProductRepository $productRepository,
        StockMovementRepository $stockMovementRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->stockMovementRepository = $stockMovementRepository;
    }

    public function getAllSales()
    {
        return $this->saleRepository->getAll();
    }

    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sale = $this->saleRepository->create($data);
            
            foreach ($data['items'] as $item) {
                $product = $this->productRepository->find($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . $item['product_id']);
                }
                
                $this->productRepository->update($item['product_id'], [
                    'stock' => $product->stock - $item['quantity']
                ]);
                
                $this->stockMovementRepository->create([
                    'product_id' => $item['product_id'],
                    'quantity' => -$item['quantity'],
                    'movement_type' => 'sale',
                    'description' => 'Stock reduced for sale: ' . $sale->id
                ]);
            }
            
            return $sale;
        });
    }

    public function getSale($id)
    {
        return $this->saleRepository->find($id);
    }

    public function cancelSale($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = $this->saleRepository->find($id);
            if (!$sale) {
                return false;
            }
            
            foreach ($sale->saleItems as $item) {
                $product = $this->productRepository->find($item->product_id);
                if ($product) {
                    $this->productRepository->update($item->product_id, [
                        'stock' => $product->stock + $item->quantity
                    ]);
                    
                    $this->stockMovementRepository->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'movement_type' => 'sale_cancellation',
                        'description' => 'Stock restored for cancelled sale: ' . $sale->id
                    ]);
                }
            }
            
            return $this->saleRepository->delete($id);
        });
    }
}