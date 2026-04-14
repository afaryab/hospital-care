<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'slug' => 'CASH',
                'name' => 'Cash',
                'id_required' => false,
                'payables' => null,
            ],
            [
                'slug' => 'CHEQUE',
                'name' => 'Cheque',
                'id_required' => true,
                'payables' => null,
            ],
            [
                'slug' => 'CARD',
                'name' => 'Card',
                'id_required' => false,
                'payables' => null,
            ],
            [
                'slug' => 'BANK_TRANSFER',
                'name' => 'Bank Transfer',
                'id_required' => true,
                'payables' => 'bank_account',
            ],
            [
                'slug' => 'PANEL',
                'name' => 'Panel',
                'id_required' => false,
                'payables' => 'panel',
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['slug' => $method['slug']],
                $method,
            );
        }
    }
}
