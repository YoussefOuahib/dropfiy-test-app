<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle(GoogleMerchantService $googleMerchantService)
    {
        try {
            // Attempt to sync the product with Google Merchant
            $googleMerchantService->syncProduct($this->product);

        } catch (\Exception $e) {
            // Log the error and update the product's sync status
            Log::error("Error syncing product {$this->product->id}: " . $e->getMessage());

        }
    }
}