<?php

namespace Database\Factories;

use App\Models\TreatmentAttachment;
use App\Models\TreatmentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TreatmentAttachment>
 */
class TreatmentAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = fake()->uuid().'.jpg';

        return [
            'treatment_record_id' => TreatmentRecord::factory(),
            'file_path' => "treatment-attachments/{$fileName}",
            'file_name' => $fileName,
            'file_type' => 'image/jpeg',
            'label' => fake()->optional()->words(2, true),
            'uploaded_by' => User::factory(),
            'uploaded_at' => now(),
        ];
    }
}
