<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\GoogleMerchantService;
use App\Jobs\SyncProductJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected GoogleMerchantService $googleMerchantService;

    public function __construct(ProductService $productService, GoogleMerchantService $googleMerchantService)
    {
        $this->productService = $productService;
        $this->googleMerchantService = $googleMerchantService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::where('is_active', true)
            ->get();
        
        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('feed'));
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return $this->respondWithProduct($product, 'Product created successfully');
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = $this->productService->updateProduct($product, $request->validated());
        return $this->respondWithProduct($updatedProduct, 'Product updated successfully');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function sync(Product $product): JsonResponse
    {
        SyncProductJob::dispatch($product);
        return response()->json(['message' => 'Product sync job has been queued']);
    }

    protected function respondWithProduct(Product $product, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'product' => new ProductResource($product),
        ]);
    }
}