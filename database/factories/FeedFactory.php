<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedFactory extends Factory
{
    protected $model = Feed::class;

    public function definition()
    {
        $name = $this->faker->words(2, true) . ' Feed';
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'last_synced_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}