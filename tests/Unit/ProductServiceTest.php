<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\Feed;
use App\Models\User;
use App\Services\ProductService;
use App\Services\GoogleMerchantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Illuminate\Database\Eloquent\Collection;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $productService;
    protected $googleMerchantService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->googleMerchantService = Mockery::mock(GoogleMerchantService::class);
        $this->productService = new ProductService($this->googleMerchantService);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_product_success()
    {
        $feedIds = Feed::factory(2)->create(['user_id' => $this->user->id])->pluck('id')->toArray();
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 10.99,
            'sku' => 'TEST123',
            'inventory' => 100,
            'feed_ids' => $feedIds
        ];

        $product = $this->productService->createProduct($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productData['name'], $product->name);
        $this->assertEquals($this->user->id, $product->user_id);
        $this->assertCount(2, $product->feed);
    }

    public function test_update_product_success()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Product',
            'price' => 15.99,
            'feed_ids' => [$feed->id]
        ];

        $updatedProduct = $this->productService->updateProduct($product, $updateData);

        $this->assertEquals('Updated Product', $updatedProduct->name);
        $this->assertEquals(15.99, $updatedProduct->price);
        $this->assertCount(1, $updatedProduct->feed);
        $this->assertEquals($feed->id, $updatedProduct->feed->first()->id);
    }

    public function test_delete_product_success()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $product->feed()->attach($feed);

        $this->productService->deleteProduct($product);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('feed_product', ['product_id' => $product->id]);
    }

    public function test_sync_product_success()
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
            'last_synced_at' => null
        ]);

        $this->googleMerchantService->shouldReceive('syncProduct')
            ->once()
            ->with(Mockery::type(Product::class))
            ->andReturn(true);

        $this->productService->syncProduct($product);

        $this->assertTrue($product->fresh()->is_active);
        $this->assertNotNull($product->fresh()->last_synced_at);
    }

    public function test_sync_product_failure()
    {
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true
        ]);

        $this->googleMerchantService->shouldReceive('syncProduct')
            ->once()
            ->with(Mockery::type(Product::class))
            ->andThrow(new \Exception('Sync failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to sync product: Sync failed');

        $this->productService->syncProduct($product);

        $this->assertFalse($product->fresh()->is_active);
    }

    public function test_get_products_for_feed()
    {
        $feed = Feed::factory()->create(['user_id' => $this->user->id]);
        $activeProducts = Product::factory(3)->create([
            'user_id' => $this->user->id,
            'is_active' => true
        ]);
        $inactiveProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false
        ]);

        $feed->products()->saveMany($activeProducts);
        $feed->products()->save($inactiveProduct);

        $products = $this->productService->getProductsForFeed($feed->id);

        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(3, $products);
        $this->assertFalse($products->contains($inactiveProduct));
    }

    public function test_create_product_throws_exception_on_failure()
    {
        // Forcing an exception by providing invalid data
        $productData = [
            'name' => 'Test Product',
            'price' => 'invalid_price', // This should cause a database exception
        ];

        $this->expectException(\Exception::class);

        $this->productService->createProduct($productData);
    }

    public function test_update_product_throws_exception_on_failure()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        // Forcing an exception by providing invalid data
        $updateData = [
            'price' => 'invalid_price', // This should cause a database exception
        ];

        $this->expectException(\Exception::class);

        $this->productService->updateProduct($product, $updateData);
    }

    public function test_delete_product_throws_exception_on_failure()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        
        // Mock the Product model to throw an exception during deletion
        $mockProduct = Mockery::mock($product);
        $mockProduct->shouldReceive('delete')->once()->andThrow(new \Exception('Delete failed'));
        $mockProduct->shouldReceive('feed->detach')->once()->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to delete product: Delete failed');

        $this->productService->deleteProduct($mockProduct);
    }
}