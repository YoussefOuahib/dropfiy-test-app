<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sku' => $this->faker->unique()->ean8,
            'inventory' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean,
            'last_synced_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}