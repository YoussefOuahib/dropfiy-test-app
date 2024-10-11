<?php 
return [
    'rate_limit_per_minute' => env('GOOGLE_MERCHANT_RATE_LIMIT', 60),
    'submission_threshold' => env('GOOGLE_MERCHANT_SUBMISSION_THRESHOLD', 24),
    'sync_threshold' => env('GOOGLE_MERCHANT_SYNC_THRESHOLD', 24),
    'currency' => env('GOOGLE_MERCHANT_CURRENCY', 'USD'),
];