<?php

namespace Database\Factories;

use App\Enum\SlipPhotoSource;
use App\Enum\SlipPhotoSubject;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\SlipPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlipPhoto>
 */
class SlipPhotoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'closing_id' => Closing::factory(),
            'transaction_id' => null,
            'subject' => SlipPhotoSubject::Patient,
            'source' => SlipPhotoSource::Manual,
            'captured_by' => User::factory(),
            'captured_at' => now(),
        ];
    }

    public function guardian(): static
    {
        return $this->state(fn (array $attributes) => [
            'subject' => SlipPhotoSubject::Guardian,
        ]);
    }

    public function auto(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => SlipPhotoSource::Auto,
        ]);
    }
}
