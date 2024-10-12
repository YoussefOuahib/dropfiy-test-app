<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\GoogleMerchantService;
use App\Jobs\SyncProductJob;
use Illuminate\Support\Facades\Gate;
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

    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Product::class);
        $products = Product::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        Gate::authorize('view', $product);
        return new ProductResource($product->load('feed'));
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        Gate::authorize('store', Product::class);
        try {
            $product = $this->productService->createProduct($request->validated());
            return $this->respondWithProduct($product, 'Product created successfully');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        Gate::authorize('update', $product);
        try {
            $updatedProduct = $this->productService->updateProduct($product, $request->all());
            return $this->respondWithProduct($updatedProduct, 'Product updated successfully');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Product $product): JsonResponse
    {
        Gate::authorize('delete', $product);
        try {
            $this->productService->deleteProduct($product);
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sync(Product $product): JsonResponse
    {
        Gate::authorize('sync', $product);
        try {
            SyncProductJob::dispatch($product);
            return response()->json(['message' => 'Product sync job has been queued']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detachFeed(Request $request, Product $product): JsonResponse
    {
        Gate::authorize('detachFeed', $product);
        try {
            $feedId = $request->feed_id;
            $product->feed()->detach($feedId);
            return response()->json(['message' => 'Feed detached from product successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function respondWithProduct(Product $product, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'product' => new ProductResource($product),
        ]);
    }
}