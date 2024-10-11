<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\ProductException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private GoogleMerchantService $googleMerchantService;

    public function __construct(GoogleMerchantService $googleMerchantService)
    {
        $this->googleMerchantService = $googleMerchantService;
    }

    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        try {
            $product = Product::create($data);
            
            if (isset($data['feed_ids'])) {
                $product->feed()->sync($data['feed_ids']);
            }

            DB::commit();
            Log::info("Product created successfully", ['product_id' => $product->id]);
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create product", ['error' => $e->getMessage()]);
            throw new \Exception("Failed to create product: " . $e->getMessage());
        }
    }

    public function updateProduct(Product $product, array $data): Product
    {
        DB::beginTransaction();
        try {
            $product->update($data);

            if (isset($data['feed_ids'])) {
                $product->feed()->sync($data['feed_ids']);
            }

            DB::commit();
            Log::info("Product updated successfully", ['product_id' => $product->id]);
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update product", ['product_id' => $product->id, 'error' => $e->getMessage()]);
            throw new \Exception("Failed to update product: " . $e->getMessage());
        }
    }

    public function deleteProduct(Product $product): void
    {
        DB::beginTransaction();
        try {
            $product->feed()->detach();
            $product->delete();
            DB::commit();
            Log::info("Product deleted successfully", ['product_id' => $product->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete product", ['product_id' => $product->id, 'error' => $e->getMessage()]);
            throw new \Exception("Failed to delete product: " . $e->getMessage());
        }
    }

    public function syncProduct(Product $product): void
    {
        try {
            $this->googleMerchantService->syncProduct($product);
            $product->update([
                'sync_status' => 'synced',
                'last_synced_at' => now(),
            ]);
            Log::info("Product synced successfully", ['product_id' => $product->id]);
        } catch (\Exception $e) {
            Log::error("Failed to sync product", ['product_id' => $product->id, 'error' => $e->getMessage()]);
            $product->update(['sync_status' => 'failed']);
            throw new \Exception("Failed to sync product: " . $e->getMessage());
        }
    }

    public function getProductsForFeed(int $feedId): \Illuminate\Database\Eloquent\Collection
    {
        return Product::whereHas('feed', function ($query) use ($feedId) {
            $query->where('feed_id', $feedId);
        })
        ->where('is_active', true)
        ->get();
    }
}