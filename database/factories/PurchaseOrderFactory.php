<?php

namespace Database\Factories;

use App\Enum\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        static $sequence = 0;
        $sequence++;
        $now = now();

        return [
            'po_number' => sprintf('PO/%s/%s/%04d', $now->format('Y'), $now->format('m'), $sequence),
            'vendor_name' => fake()->company(),
            'status' => PurchaseOrderStatus::Draft->value,
            'total_amount' => fake()->randomFloat(2, 1000, 100000),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PurchaseOrderStatus::Received->value,
            'approved_at' => now()->subDay(),
            'received_at' => now(),
        ]);
    }
}
