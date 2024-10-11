<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Feed;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::factory()
            ->count(50)
            ->create()
            ->each(function ($product) {
                $feeds = Feed::inRandomOrder()->take(rand(1, 3))->get();
                $product->feed()->attach($feeds);
            });
    }
}