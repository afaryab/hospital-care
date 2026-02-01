<?php
namespace Processton\AbacusDatabase\Seeders;

use Processton\Abacus\Models\AbacusChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ASSETS
            ['code' => '1000', 'name' => 'Assets', 'base_type' => 'asset', 'is_group' => true],
            ['code' => '1100', 'name' => 'Current Assets', 'base_type' => 'asset', 'is_group' => true, 'parent' => '1000'],
            ['code' => '1110', 'name' => 'Cash in Hand', 'base_type' => 'asset', 'type' => 'cash', 'parent' => '1100'],
            ['code' => '1120', 'name' => 'Bank Accounts', 'base_type' => 'asset', 'type' => 'bank', 'parent' => '1100'],
            ['code' => '1130', 'name' => 'Accounts Receivable', 'base_type' => 'asset', 'type' => 'receivables', 'parent' => '1100'],
            ['code' => '1140', 'name' => 'Inventory', 'base_type' => 'asset', 'type' => 'inventory', 'parent' => '1100'],
            ['code' => '1150', 'name' => 'Prepaid Expenses', 'base_type' => 'asset', 'type' => 'prepaid', 'parent' => '1100'],

            ['code' => '1200', 'name' => 'Fixed Assets', 'base_type' => 'asset', 'is_group' => true, 'parent' => '1000'],
            ['code' => '1210', 'name' => 'Furniture & Fixtures', 'base_type' => 'asset', 'type' => 'fixed_asset', 'parent' => '1200'],
            ['code' => '1220', 'name' => 'Office Equipment', 'base_type' => 'asset', 'type' => 'fixed_asset', 'parent' => '1200'],
            ['code' => '1230', 'name' => 'Vehicles', 'base_type' => 'asset', 'type' => 'fixed_asset', 'parent' => '1200'],
            ['code' => '1240', 'name' => 'Accumulated Depreciation', 'base_type' => 'asset', 'type' => 'contra_asset', 'parent' => '1200'],

            // LIABILITIES
            ['code' => '2000', 'name' => 'Liabilities', 'base_type' => 'liability', 'is_group' => true],
            ['code' => '2100', 'name' => 'Current Liabilities', 'base_type' => 'liability', 'is_group' => true, 'parent' => '2000'],
            ['code' => '2110', 'name' => 'Accounts Payable', 'base_type' => 'liability', 'type' => 'payables', 'parent' => '2100'],
            ['code' => '2120', 'name' => 'Taxes Payable', 'base_type' => 'liability', 'type' => 'tax', 'parent' => '2100'],
            ['code' => '2130', 'name' => 'Accrued Expenses', 'base_type' => 'liability', 'type' => 'accruals', 'parent' => '2100'],

            ['code' => '2200', 'name' => 'Long-Term Liabilities', 'base_type' => 'liability', 'is_group' => true, 'parent' => '2000'],
            ['code' => '2210', 'name' => 'Loans Payable', 'base_type' => 'liability', 'type' => 'loan', 'parent' => '2200'],

            // EQUITY
            ['code' => '3000', 'name' => 'Equity', 'base_type' => 'equity', 'is_group' => true],
            ['code' => '3100', 'name' => 'Owner’s Capital', 'base_type' => 'equity', 'type' => 'capital', 'parent' => '3000'],
            ['code' => '3200', 'name' => 'Owner Drawings', 'base_type' => 'equity', 'type' => 'drawings', 'parent' => '3000'],
            ['code' => '3300', 'name' => 'Retained Earnings', 'base_type' => 'equity', 'type' => 'retained_earnings', 'parent' => '3000'],

            // INCOME
            ['code' => '4000', 'name' => 'Income', 'base_type' => 'income', 'is_group' => true],
            ['code' => '4100', 'name' => 'Sales Revenue', 'base_type' => 'income', 'type' => 'sales', 'parent' => '4000'],
            ['code' => '4200', 'name' => 'Service Revenue', 'base_type' => 'income', 'type' => 'services', 'parent' => '4000'],
            ['code' => '4300', 'name' => 'Other Income', 'base_type' => 'income', 'type' => 'other_income', 'parent' => '4000'],

            // EXPENSES
            ['code' => '5000', 'name' => 'Expenses', 'base_type' => 'expense', 'is_group' => true],
            ['code' => '5100', 'name' => 'Cost of Goods Sold (COGS)', 'base_type' => 'expense', 'type' => 'cogs', 'parent' => '5000'],
            ['code' => '5200', 'name' => 'Salaries & Wages', 'base_type' => 'expense', 'type' => 'salaries', 'parent' => '5000'],
            ['code' => '5300', 'name' => 'Rent Expense', 'base_type' => 'expense', 'type' => 'rent', 'parent' => '5000'],
            ['code' => '5400', 'name' => 'Utilities', 'base_type' => 'expense', 'type' => 'utilities', 'parent' => '5000'],
            ['code' => '5500', 'name' => 'Depreciation Expense', 'base_type' => 'expense', 'type' => 'depreciation', 'parent' => '5000'],
            ['code' => '5600', 'name' => 'Office Supplies', 'base_type' => 'expense', 'type' => 'supplies', 'parent' => '5000'],
            ['code' => '5700', 'name' => 'Marketing & Advertising', 'base_type' => 'expense', 'type' => 'marketing', 'parent' => '5000'],
            ['code' => '5800', 'name' => 'Travel & Entertainment', 'base_type' => 'expense', 'type' => 'travel', 'parent' => '5000'],
            ['code' => '5900', 'name' => 'Other Expenses', 'base_type' => 'expense', 'type' => 'misc', 'parent' => '5000'],
        ];

        $map = [];

        // First pass: create group accounts
        foreach ($accounts as $acc) {
            if (!empty($acc['is_group'])) {
                $map[$acc['code']] = AbacusChartOfAccount::create([
                    'code'       => $acc['code'],
                    'name'       => $acc['name'],
                    'base_type'  => $acc['base_type'],
                    'type'       => $acc['type'] ?? null,
                    'parent_id'  => isset($acc['parent']) ? ($map[$acc['parent']]->id ?? null) : null,
                    'is_group'   => true,
                ]);
            }
        }

        // Second pass: create leaf accounts
        foreach ($accounts as $acc) {
            if (empty($acc['is_group'])) {
                AbacusChartOfAccount::create([
                    'code'       => $acc['code'],
                    'name'       => $acc['name'],
                    'base_type'  => $acc['base_type'],
                    'type'       => $acc['type'] ?? null,
                    'parent_id'  => isset($acc['parent']) ? ($map[$acc['parent']]->id ?? null) : null,
                    'is_group'   => false,
                ]);
            }
        }
    }
}
