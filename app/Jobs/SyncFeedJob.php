<?php

namespace App\Jobs;

use App\Models\Feed;
use App\Services\GoogleMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Feed $feed;

    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    public function handle(GoogleMerchantService $googleMerchantService)
    {
            Log::info('Starting feed submission', ['feed_id' => $this->feed->id]);

            $googleMerchantService->syncFeed($this->feed);

    }
}