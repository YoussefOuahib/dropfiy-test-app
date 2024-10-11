<?php 
namespace App\Services;

use App\Interfaces\GoogleMerchantApiClientInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleMerchantApiClient implements GoogleMerchantApiClientInterface
{
    private int $rateLimitPerMinute;

    public function __construct(int $rateLimitPerMinute = 60)
    {
        $this->rateLimitPerMinute = $rateLimitPerMinute;
    }

    public function submitProductFeed() : mixed
    {
        if (!$this->checkRateLimit()) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        // Simulate processing time
        sleep(1);

        // Simulate a successful submission 80% of the time
        if (rand(1, 100) <= 80) {
            return response()->json(['message' => 'Product feed submitted successfully'], 200);
        } else {
            return $this->errorResponse('Error submitting product feed', 400);
        }
    }

    public function getFeedStatus(int $feedId): array
    {
        if (!$this->checkRateLimit()) {
            return $this->errorResponse('Rate limit exceeded', 429);
        }

        // Simulate processing time
        sleep(1);

        $statuses = ['processing', 'success', 'error'];
        $randomStatus = $statuses[array_rand($statuses)];

        return $this->successResponse([
            'feed_id' => $feedId,
            'status' => $randomStatus,
        ]);
    }

    private function checkRateLimit(): bool
    {
        $currentMinute = now()->format('YmdHi');
        $requestCount = Cache::get('google_merchant_api_requests:' . $currentMinute, 0);

        if ($requestCount >= $this->rateLimitPerMinute) {
            return false;
        }

        Cache::put('google_merchant_api_requests:' . $currentMinute, $requestCount + 1, 60);
        return true;
    }

    private function successResponse($data): array
    {
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    private function errorResponse(string $message, int $code): array
    {
        Log::error("Google Merchant API Error: $message");
        return [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $code,
            ],
        ];
    }
}