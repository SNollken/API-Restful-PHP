<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
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
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->getAllProducts();
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

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
            $this->authorize('view', $product);
            return new ProductResource($product);
        } else {
            return response()->json(["message" => "Product not found"], 404);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->getProduct($id);
        if ($product) {
            $this->authorize('update', $product);

            $product = $this->productService->updateProduct($id, $request->validated());
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
        $product = $this->productService->getProduct($id);
        if ($product) {
            $this->authorize('delete', $product);

            $this->productService->deleteProduct($id);
            return response()->json(["message" => "Product deleted successfully!"], 202);
        } else {
            return response()->json(["message" => "Product not found"], 404);
        }
    }
}
