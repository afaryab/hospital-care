<?php

namespace Database\Factories;

use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DmsDocument>
 */
class DmsDocumentFactory extends Factory
{
    protected $model = DmsDocument::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'folder_id' => DmsFolder::factory(),
            'name' => fake()->unique()->words(3, true).'.pdf',
            'status' => 'draft',
            'is_locked' => false,
            'current_version' => 1,
            'created_by' => User::factory(),
        ];
    }
}
