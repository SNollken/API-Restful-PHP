<?php

namespace App\Services;

use App\Repositories\SaleRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StockMovementRepository;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\SaleAlreadyCancelledException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            $lineItems = [];
            $stockUpdates = [];
            $totalAmount = 0;

            foreach ($data['items'] as $item) {
                $product = $this->productRepository->findForUpdate($item['product_id']);
                if (!$product) {
                    throw new ModelNotFoundException();
                }

                $quantity = (int) $item['quantity'];
                if ($product->stock < $quantity) {
                    throw new InsufficientStockException($product->id, $product->stock, $quantity);
                }

                $unitPrice = array_key_exists('unit_price', $item) && $item['unit_price'] !== null
                    ? $item['unit_price']
                    : $product->price;

                $lineItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];

                $stockUpdates[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];

                $totalAmount += $quantity * $unitPrice;
            }

            $sale = $this->saleRepository->create([
                'total_amount' => round($totalAmount, 2),
                'items' => $lineItems,
            ]);

            foreach ($stockUpdates as $update) {
                $product = $update['product'];
                $quantity = $update['quantity'];

                $this->productRepository->update($product->id, [
                    'stock' => $product->stock - $quantity
                ]);

                $this->stockMovementRepository->create([
                    'product_id' => $product->id,
                    'quantity' => -$quantity,
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

            if ($sale->status === 'cancelled') {
                throw new SaleAlreadyCancelledException($sale->id);
            }

            foreach ($sale->saleItems as $item) {
                $product = $this->productRepository->findForUpdate($item->product_id);
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

            return $this->saleRepository->cancel($sale);
        });
    }
}
