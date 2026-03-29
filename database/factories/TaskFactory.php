<?php

namespace Database\Factories;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;
        $now = now();

        return [
            'task_number' => sprintf('TSK/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'priority' => TaskPriority::Medium->value,
            'status' => TaskStatus::Todo->value,
            'assigned_to' => User::factory(),
            'assigned_by' => User::factory(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed->value,
            'completed_at' => now(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TaskPriority::Urgent->value,
        ]);
    }
}
