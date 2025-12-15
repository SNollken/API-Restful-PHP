<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json([
            "message" => "Product created successfully!",
            "data" => new ProductResource($product)
        ], 201);
    }

    public function show($id)
    {
        $product = $this->productService->getProduct($id);
        if ($product) {
            return new ProductResource($product);
        } else {
            return response()->json(["message" => "Product not found"], 404);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        if ($product) {
            return response()->json([
                "message" => "Product updated successfully!",
                "data" => new ProductResource($product)
            ], 200);
        } else {
            return response()->json(["message" => "Product not found"], 404);
        }
    }

    public function destroy($id)
    {
        $deleted = $this->productService->deleteProduct($id);
        if ($deleted) {
            return response()->json(["message" => "Product deleted successfully!"], 202);
        } else {
            return response()->json(["message" => "Product not found"], 404);
        }
    }
}