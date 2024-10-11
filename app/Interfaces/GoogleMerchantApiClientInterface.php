<?php
namespace App\Interfaces;

interface GoogleMerchantApiClientInterface
{
    public function submitProductFeed(): mixed;
    public function getFeedStatus(int $feedId): array;
}
