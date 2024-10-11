<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Jobs\GenerateFeedJob;
use App\Jobs\SubmitFeedJob;
use Illuminate\Console\Command;

class GenerateAndSubmitFeeds extends Command
{
    protected $signature = 'feeds:generate-and-submit';
    protected $description = 'Generate and submit all feeds to Google Merchant';

    public function handle()
    {
        Feed::all()->each(function ($feed) {
            GenerateFeedJob::dispatch($feed)->chain([
                new SubmitFeedJob($feed),
            ]);
        });

        $this->info('Feed generation and submission jobs have been dispatched.');
    }
}
