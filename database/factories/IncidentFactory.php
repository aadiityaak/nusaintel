<?php

namespace Database\Factories;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'public_id' => (string) Str::uuid(),
            'title' => fake()->sentence(5),
            'summary' => fake()->paragraph(),
            'category' => fake()->word(),
            'occurred_at' => fake()->optional()->date(),
            'occurred_at_precision' => fake()->randomElement(['exact', 'approximate', 'unknown']),
            'published_at' => fake()->optional()->date(),
            'kecamatan' => fake()->word(),
            'latitude' => null,
            'longitude' => null,
            'status' => 'draft',
            'publication_basis' => null,
            'published_at_app' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
