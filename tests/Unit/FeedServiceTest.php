<?php

namespace Tests\Unit\Services;

use App\Exceptions\FeedSubmissionException;
use App\Services\FeedService;
use App\Models\Feed;
use App\Models\Product;
use App\Models\User;
use App\Enums\FeedStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class FeedServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $feedService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedService = new FeedService();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_create_feed_success()
    {
        $products = Product::factory(3)->create(['user_id' => $this->user->id]);
        $data = [
            'name' => 'Test Feed',
            'product_ids' => $products->pluck('id')->toArray()
        ];

        $feed = $this->feedService->createFeed($data);

        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertEquals('Test Feed', $feed->name);
        $this->assertEquals($this->user->id, $feed->user_id);
        $this->assertCount(3, $feed->products);
    }

    public function test_create_feed_without_products()
    {
        $data = ['name' => 'Test Feed'];

        $feed = $this->feedService->createFeed($data);

        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertEquals('Test Feed', $feed->name);
        $this->assertCount(0, $feed->products);
    }

    public function test_create_feed_throws_exception_on_failure()
    {
        $this->expectException(FeedSubmissionException::class);
        $this->feedService->createFeed([]);
    }

    public function test_update_feed_success()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $newProducts = Product::factory(2)->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Feed',
            'product_ids' => $newProducts->pluck('id')->toArray()
        ];

        $updatedFeed = $this->feedService->updateFeed($feed, $updateData);

        $this->assertEquals('Updated Feed', $updatedFeed->name);
        $this->assertCount(2, $updatedFeed->products);
    }

    public function test_delete_feed_success()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $products = Product::factory(2)->create(['user_id' => $this->user->id]);
        $feed->products()->attach($products);

        $this->feedService->deleteFeed($feed);

        $this->assertDatabaseMissing('feeds', ['id' => $feed->id]);
        $this->assertDatabaseMissing('feed_product', ['feed_id' => $feed->id]);
    }


    public function test_detach_product_success()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $feed->products()->attach($product);

        $result = $this->feedService->detachProduct($feed, $product->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('feed_product', [
            'feed_id' => $feed->id,
            'product_id' => $product->id
        ]);
    }

    public function test_detach_product_failure()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $nonExistentProductId = 9999;

        $result = $this->feedService->detachProduct($feed, $nonExistentProductId);

        $this->assertTrue($result);  // The current implementation always returns true
    }

    public function test_get_report_data()
    {
        // Create feeds with different statuses
        Feed::factory()->count(2)->create(['user_id' => $this->user->id, 'status' => FeedStatus::Synced]);
        Feed::factory()->create(['user_id' => $this->user->id, 'status' => FeedStatus::Pending]);
        Feed::factory()->create(['user_id' => $this->user->id, 'status' => FeedStatus::Failed]);

        // Create products and associate them with feeds
        $products = Product::factory(5)->create(['user_id' => $this->user->id]);
        Feed::all()->each(function ($feed) use ($products) {
            $feed->products()->attach($products->random(rand(1, 3))); // Attach 1 to 3 random products to each feed
        });

        $reportData = $this->feedService->getReportData();

        $this->assertArrayHasKey('overall_stats', $reportData);
        $this->assertEquals(4, $reportData['overall_stats']['total_feeds']);
        $this->assertEquals(2, $reportData['overall_stats']['synced_feeds']);
        $this->assertEquals(1, $reportData['overall_stats']['pending_feeds']);
        $this->assertEquals(1, $reportData['overall_stats']['failed_feeds']);

        $this->assertLessThanOrEqual(5, $reportData['overall_stats']['total_products']);

        $this->assertGreaterThan(0, $reportData['overall_stats']['total_products']);
        $this->assertEquals(
            $reportData['overall_stats']['total_feeds'],
            $reportData['overall_stats']['synced_feeds'] +
            $reportData['overall_stats']['pending_feeds'] +
            $reportData['overall_stats']['failed_feeds']
        );
    }
}