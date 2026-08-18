<?php

namespace Database\Factories;

use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DmsFolder>
 */
class DmsFolderFactory extends Factory
{
    protected $model = DmsFolder::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->unique()->words(2, true),
            'parent_id' => null,
            'path' => '/',
            'is_system' => false,
            'created_by' => User::factory(),
        ];
    }

    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
        ]);
    }

    public function childOf(DmsFolder $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'path' => $parent->path.$parent->id.'/',
        ]);
    }
}
