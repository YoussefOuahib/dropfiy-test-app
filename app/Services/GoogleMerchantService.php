<?php
namespace App\Services;

use App\Models\Feed;
use App\Models\Product;
use App\Interfaces\GoogleMerchantApiClientInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleMerchantService
{
    private GoogleMerchantApiClientInterface $apiClient;

    public function __construct(GoogleMerchantApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function syncFeed(Feed $feed): Feed
    {
        try {
            Log::info("Submitting feed for feed ID: {$feed->id}");
            $feedContent = $this->generateFeedContent($feed);
            $response = $this->apiClient->submitProductFeed();

            if ($response->getStatusCode() === 200) {
                $feed->markAsSynced();
                Log::info("Feed submitted successfully for feed ID: {$feed->id}");
                return $feed;
            } else {
                $feed->markAsFailed();
                throw new \Exception($response['error']['message'], $response['error']['code']);
            }
        } catch (\Exception $e) {
            Log::error("Feed submission failed for feed ID: {$feed->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function generateFeedContent(Feed $feed): array
    {
        try {
            Log::info("Generating feed content for feed ID: {$feed->id}");
            $products = $this->getProductsForFeed($feed);
            $feedContent = $this->formatProductsForFeed($products);
            Log::info("Feed content generated successfully for feed ID: {$feed->id}");
            return $feedContent;
        } catch (\Exception $e) {
            Log::error("Error generating feed content for feed ID: {$feed->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }



    public function checkFeedStatus(Feed $feed): array
    {
        try {
            Log::info("Checking feed status for feed ID: {$feed->id}");
            $response = $this->apiClient->getFeedStatus($feed->id);

            if ($response['success']) {
                $this->updateFeedStatus($feed, $response['data']);
                Log::info("Feed status updated for feed ID: {$feed->id}");
                return $response['data'];
            } else {
                throw new \Exception($response['error']['message'], $response['error']['code']);
            }
        } catch (\Exception $e) {
            Log::error("Feed status check failed for feed ID: {$feed->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function syncProducts(Feed $feed): void
    {
        DB::beginTransaction();
        try {
            Log::info("Starting product sync for feed ID: {$feed->id}");
            $products = $this->getProductsForFeed($feed);

            foreach ($products as $product) {
                $this->syncProduct($product);
            }

            $feed->markAsSynced();
            DB::commit();
            Log::info("Products synced successfully for feed ID: {$feed->id}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Product sync failed for feed ID: {$feed->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function getProductsForFeed(Feed $feed): Collection
    {
        return $feed->products()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('last_synced_at')
                    ->orWhere('last_synced_at', '<=', now()->subHours(config('google_merchant.sync_threshold', 24)));
            })
            ->get();
    }

    private function formatProductsForFeed(Collection $products): array
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->name,
                'description' => $product->description,
                'availability' => $product->inventory > 0 ? 'in stock' : 'out of stock',
                'price' => $product->price . ' ' . config('google_merchant.currency', 'USD'),
                'sku' => $product->sku,
            ];
        })->toArray();
    }

    public function syncProduct(Product $product): void
    {
        try {
            $product->update([
                'is_active' => true,
                'last_synced_at' => now(),
            ]);
            Log::info("Product synced: {$product->id}");
        } catch (\Exception $e) {
            $product->update([
                'is_active' => false,
            ]);
            Log::error("Failed to sync product {$product->id}: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateFeedStatus(Feed $feed, array $statusData): void
    {
        $feed->update([
            'last_synced_at' => Carbon::now(),
        ]);
    }
}
