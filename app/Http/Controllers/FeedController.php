<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFeedRequest;
use App\Http\Requests\UpdateFeedRequest;
use App\Http\Resources\FeedResource;
use App\Jobs\SyncFeedJob;
use App\Models\Feed;
use App\Services\FeedService;
use App\Services\GoogleMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    protected FeedService $feedService;
    protected GoogleMerchantService $googleMerchantService;

    public function __construct(FeedService $feedService, GoogleMerchantService $googleMerchantService)
    {
        $this->feedService = $feedService;
        $this->googleMerchantService = $googleMerchantService;
    }

    public function index(): AnonymousResourceCollection
    {
        $feeds = Feed::orderBy('created_at', 'desc')->get();
        return FeedResource::collection($feeds);
    }

    public function show(Feed $feed): FeedResource
    {
        return new FeedResource($feed->load('products'));
    }

    public function store(CreateFeedRequest $request): JsonResponse
    {
        $feed = $this->feedService->createFeed($request->validated());
        return $this->respondWithFeed($feed, 'Feed created successfully');
    }

    public function update(UpdateFeedRequest $request, Feed $feed): JsonResponse
    {
        $updatedFeed = $this->feedService->updateFeed($feed, $request->validated());
        return $this->respondWithFeed($updatedFeed, 'Feed updated successfully');
    }

    public function destroy(Feed $feed): JsonResponse
    {
        $this->feedService->deleteFeed($feed);
        return response()->json(['message' => 'Feed deleted successfully']);
    }

    public function sync(Feed $feed): JsonResponse
    {
        SyncFeedJob::dispatch($feed);
        return response()->json(['message' => 'Feed generation and submission job has been queued']);
    }

    public function detachProduct(Request $request, Feed $feed): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $result = $this->feedService->detachProduct($feed, $validated['product_id']);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Product detached successfully',
                'feed' => new FeedResource($feed->fresh('products')),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to detach product from feed',
            ], 422);
        }
    }


    protected function respondWithFeed(Feed $feed, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'feed' => new FeedResource($feed),
        ]);
    }
}