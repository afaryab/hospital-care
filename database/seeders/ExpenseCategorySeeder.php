<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // allow_petty_cash  → small, day-to-day, low-value disbursements from the petty cash box
        // allow_voucher     → formal/large payments that require a signed expense voucher & approval
        $array = collect([
            [
                'name' => 'Outdoor Doctors Payments',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => false,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Rent or mortgage payments',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Home office costs',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => false,
            ],
            [
                'name' => 'Utilities',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => false,
            ],
            [
                'name' => 'Furniture, equipment, and machinery',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Office supplies',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => false,
            ],
            [
                'name' => 'Advertising and marketing',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Website and software expenses',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Entertainment',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => false,
            ],
            [
                // Meals and small travel reimbursed from petty cash; larger travel advances use a voucher
                'name' => 'Business meals and travel expenses',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                // Fuel / tolls → petty cash; major repairs → voucher
                'name' => 'Vehicle expenses',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Payroll',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Employee benefits',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Taxes',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Business insurance',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Business licenses and permits',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Interest payments and bank fees',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                // Small annual memberships can go via petty cash; larger ones need a voucher
                'name' => 'Membership fees',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Professional fees and business services',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Training and education',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Refund',
                'type' => 'RFND',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Discount',
                'type' => 'DISC',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => true,
                'allow_voucher' => false,
            ],
            [
                'name' => 'Purchase of Medical Equipments',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                // Small spare parts / consumables → petty cash; major repairs → voucher
                'name' => 'Repair of Medical Equipments',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                // Minor fixes → petty cash; contractors / large works → voucher
                'name' => 'Hospital Repair and Maintenance',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                // Bulk medicine purchases always go through a formal voucher
                'name' => 'Medicine',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Electricity Bill',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Sui Gas Bill',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Wasa Bill',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Loan / Advance Salary',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'OTA Share',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Surgeon fee',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Anasthatic fee',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Pediatrician',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Lab',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Assistant',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Medicine',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                // Miscellaneous inpatient costs can be small (petty cash) or large (voucher)
                'name' => 'Miscellaneous',
                'type' => 'INPT',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Oxygen',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => false,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'PTCL Bill',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Dental Assistant Payments',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
            [
                // Dental consumables (gloves, impression material) can go via petty cash
                'name' => 'Dental Expense',
                'type' => '',
                'pay_doc' => false,
                'pay_others' => false,
                'pay_users' => false,
                'allow_petty_cash' => true,
                'allow_voucher' => true,
            ],
            [
                'name' => 'Inpatient Doctor Payment',
                'type' => '',
                'pay_doc' => true,
                'pay_others' => true,
                'pay_users' => true,
                'allow_petty_cash' => false,
                'allow_voucher' => true,
            ],
        ]);

        $existingCategories = ExpenseCategory::pluck('name')->toArray();
        $array = $array->filter(fn ($item) => ! in_array($item['name'], $existingCategories));

        $records = [];

        foreach ($array as $item) {
            $records[] = ExpenseCategory::firstOrCreate(
                ['name' => $item['name']],
                [
                    'type' => $item['type'],
                    'pay_doc' => $item['pay_doc'],
                    'pay_others' => $item['pay_others'],
                    'pay_users' => $item['pay_users'],
                    'allow_petty_cash' => $item['allow_petty_cash'],
                    'allow_voucher' => $item['allow_voucher'],
                ],
            );
        }

        Log::info('ExpenseCategorySeeder: Created '.count($records).' new categories');

        $this->command->info('ExpenseCategorySeeder: Created '.count($records).' new categories');
    }
}
