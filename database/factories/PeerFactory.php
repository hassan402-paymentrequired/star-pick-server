<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peer>
 */
class PeerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'peer_id' => Str::uuid(),
            'name' => $this->faker->name(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(), // fallback to factory if users not seeded
            'amount' => $this->faker->randomFloat(8, 10, 1000),
            'private' => $this->faker->boolean(),
            'limit' => $this->faker->optional()->numberBetween(1, 100),
            'sharing_ratio' => $this->faker->numberBetween(1, 2),
        ];
    }
}
