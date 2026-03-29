<?php

namespace Database\Factories;

use App\Models\Icd10Code;
use Illuminate\Database\Eloquent\Factories\Factory;

class Icd10CodeFactory extends Factory
{
    protected $model = Icd10Code::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('?##.#'),
            'description' => fake()->sentence(),
            'category' => fake()->words(3, true),
            'is_active' => true,
        ];
    }
}
