<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Source>
 */
class SourceFactory extends Factory
{
    protected $model = Source::class;

    public function definition(): array
    {
        $domain = fake()->unique()->domainName();

        return [
            'url' => 'https://'.$domain.'/articles/'.fake()->uuid(),
            'domain' => $domain,
            'publisher' => fake()->company(),
            'source_type' => 'unknown',
            'title' => fake()->sentence(),
            'published_at' => fake()->optional()->date(),
            'excerpt' => fake()->sentence(),
            'content_hash' => null,
            'fetch_status' => 'pending',
            'fetch_error' => null,
        ];
    }
}
