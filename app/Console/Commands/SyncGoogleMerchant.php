<?php

namespace App\Console\Commands;

use App\Jobs\GenerateFeedJob;
use App\Models\Feed;
use Illuminate\Console\Command;

class SyncGoogleMerchant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:google-merchant';
    protected $description = 'Sync product feeds with Google Merchant Center';

    public function handle()
    {
        $feeds = Feed::all();

        foreach ($feeds as $feed) {
            GenerateFeedJob::dispatch($feed);
        }

        $this->info('Feed generation jobs have been dispatched.');
    }
}
