<?php

namespace Database\Factories;

use App\Models\DmsShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DmsShare>
 */
class DmsShareFactory extends Factory
{
    protected $model = DmsShare::class;

    public function definition(): array
    {
        return [
            'grantee_type' => DmsShare::GRANTEE_USER,
            'grantee_value' => (string) User::factory()->create()->id,
            'ability' => 'view',
            'created_by' => User::factory(),
        ];
    }
}
