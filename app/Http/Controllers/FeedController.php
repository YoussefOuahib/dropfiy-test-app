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
use Illuminate\Support\Facades\Gate;


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
        Gate::authorize('viewAny', Feed::class);
        $feeds = Feed::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
        return FeedResource::collection($feeds);
    }
    
    public function show(Feed $feed): FeedResource
    {
        Gate::authorize('view', $feed);
        return new FeedResource($feed->load('products'));
    }
    
    public function store(CreateFeedRequest $request): JsonResponse
    {
        Gate::authorize('create', Feed::class);
        $feed = $this->feedService->createFeed($request->validated());
        return $this->respondWithFeed($feed, 'Feed created successfully');
    }
    
    public function update(UpdateFeedRequest $request, Feed $feed): JsonResponse
    {
        Gate::authorize('update', $feed);
        $updatedFeed = $this->feedService->updateFeed($feed, $request->validated());
        return $this->respondWithFeed($updatedFeed, 'Feed updated successfully');
    }
    
    public function destroy(Feed $feed): JsonResponse
    {
        Gate::authorize('delete', $feed);
        $this->feedService->deleteFeed($feed);
        return response()->json(['message' => 'Feed deleted successfully']);
    }
    
    public function sync(Feed $feed): JsonResponse
    {
        Gate::authorize('sync', $feed);
        SyncFeedJob::dispatch($feed);
        return response()->json(['message' => 'Feed generation and submission job has been queued']);
    }
    
    public function detachProduct(Request $request, Feed $feed): JsonResponse
    {
        Gate::authorize('detachProduct', $feed);
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
    
    public function getReport(): JsonResponse
    {
        Gate::authorize('viewReport', Feed::class);
        $reportData = $this->feedService->getReportData();
        return response()->json($reportData);
    }

    protected function respondWithFeed(Feed $feed, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'feed' => new FeedResource($feed),
        ]);
    }
}