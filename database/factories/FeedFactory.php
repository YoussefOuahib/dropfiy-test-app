<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    protected $model = Feed::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true) . ' Feed',
            'last_synced_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'last_submitted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}