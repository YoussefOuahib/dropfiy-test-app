<?php

namespace App\Services;

use App\Enums\FeedStatus;
use App\Models\Feed;
use App\Models\Product;
use App\Exceptions\FeedSubmissionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeedService
{
    public function createFeed(array $data): Feed
    {
        DB::beginTransaction();
        try {
            $feed = Feed::create([
                'name' => $data['name'],
                'user_id' => auth()->user()->id
            ]);

            if (isset($data['product_ids'])) {
                $this->attachProducts($feed, $data['product_ids']);
            }

            DB::commit();
            Log::info('Feed created successfully', ['feed_id' => $feed->id]);
            return $feed;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create feed', ['error' => $e->getMessage()]);
            throw new FeedSubmissionException('Failed to create feed: ' . $e->getMessage());
        }
    }

    public function updateFeed(Feed $feed, array $data): Feed
    {
        DB::beginTransaction();
        try {
            $feed->update([
                'name' => $data['name'] ?? $feed->name,
            ]);

            if (isset($data['product_ids'])) {
                $this->syncProducts($feed, $data['product_ids']);
            }

            DB::commit();
            Log::info('Feed updated successfully', ['feed_id' => $feed->id]);
            return $feed->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update feed', ['feed_id' => $feed->id, 'error' => $e->getMessage()]);
            throw new FeedSubmissionException('Failed to update feed: ' . $e->getMessage());
        }
    }

    public function deleteFeed(Feed $feed): void
    {
        DB::beginTransaction();
        try {
            $feed->products()->detach();
            $feed->delete();

            DB::commit();
            Log::info('Feed deleted successfully', ['feed_id' => $feed->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete feed', ['feed_id' => $feed->id, 'error' => $e->getMessage()]);
            throw new FeedSubmissionException('Failed to delete feed: ' . $e->getMessage());
        }
    }

    protected function attachProducts(Feed $feed, array $productIds): void
    {
        $validProductIds = Product::whereIn('id', $productIds)->pluck('id');
        $feed->products()->attach($validProductIds);
    }

    protected function syncProducts(Feed $feed, array $productIds): void
    {
        $validProductIds = Product::whereIn('id', $productIds)->pluck('id');
        $feed->products()->sync($validProductIds);
    }

    public function detachProduct(Feed $feed, int $productId): bool
    {
        try {
            DB::beginTransaction();

            $feed->products()->detach($productId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to detach product from feed: ' . $e->getMessage());
            return false;
        }
    }

    public function getReportData(): array
    {
        
        return [
            'overall_stats' => $this->getOverallStats(),
        ];
    }

    private function getOverallStats(): array
    {
        $userId = auth()->id();

        $feedStats = Feed::where('user_id', $userId)
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalProducts = Product::whereHas('feed', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

            return [
            'total_feeds' => array_sum($feedStats),
            'synced_feeds' => $feedStats[FeedStatus::Synced->value] ?? 0,
            'pending_feeds' => $feedStats[FeedStatus::Pending->value] ?? 0,
            'failed_feeds' => $feedStats[FeedStatus::Failed->value] ?? 0,
            'total_products' => $totalProducts,
        ];
    }


}