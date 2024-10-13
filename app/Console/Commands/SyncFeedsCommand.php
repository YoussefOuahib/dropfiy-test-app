<?php

namespace App\Console\Commands;

use App\Jobs\SyncFeedJob;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncFeedsCommand extends Command
{
    protected $signature = 'feeds:sync';
    protected $description = 'Sync feeds for users based on their sync_time setting';

    public function handle()
    {
        User::has('feeds')->with(['feeds', 'settings'])->chunk(100, function ($users) {
            foreach ($users as $user) {
                $syncTime = $user->settings->sync_time ?? 60; // Default to 1 hour if not set
                
                if ($this->shouldSyncForUser($syncTime, $user->last_auto_synced_at)) {
                    $this->syncUserFeeds($user);
                    $this->info("Synced feeds for user ID: {$user->id}");
                } else {
                    $this->info("Skipped syncing for user ID: {$user->id} (Not due yet)");
                }
            }
        });
    }

    private function shouldSyncForUser(int $syncTime, ?string $lastAutoSyncedAt): bool
    {
        if (!$lastAutoSyncedAt) {
            return true; // If never auto-synced, do it now
        }

        $lastAutoSync = Carbon::parse($lastAutoSyncedAt);
        $minutesSinceLastSync = $lastAutoSync->diffInMinutes(now());

        return $minutesSinceLastSync >= $syncTime;
    }

    private function syncUserFeeds(User $user): void
    {
        $user->feeds->each(fn($feed) => SyncFeedJob::dispatch($feed));

        $user->last_auto_synced_at = now();
        $user->save();
    }
}