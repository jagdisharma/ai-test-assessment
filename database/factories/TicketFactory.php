<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::ulid()->toBase32(),
            'subject' => fake()->sentence,
            'body' => fake()->paragraph,
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved']),
            'category' => fake()->optional()->randomElement(['billing', 'technical', 'account']),
            'note' => fake()->optional()->sentence,
            'explanation' => fake()->optional()->sentence,
            'confidence' => fake()->optional()->randomFloat(2, 0.6, 1.0),
        ];
    }
}
