<?php

namespace Database\Factories;

use App\Models\DmsClassification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DmsClassification>
 */
class DmsClassificationFactory extends Factory
{
    protected $model = DmsClassification::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'code' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'security_level' => fake()->randomElement(['public', 'internal', 'confidential', 'restricted']),
            'retention_years' => fake()->optional()->numberBetween(1, 10),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
