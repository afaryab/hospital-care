<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = collect([
            [
                "name" => "Outdoor Doctors Payments",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>false,
                "pay_users"=>false
            ],
            [
                "name" => "Rent or mortgage payments",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Home office costs",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Utilities",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Furniture, equipment, and machinery",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Office supplies",
                "type"=>"",
                "pay_doc"=>"0",
                "pay_others"=>"1",
                "pay_users"=>"0"
            ],
            [
                "name" => "Advertising and marketing",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Website and software expenses",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Entertainment",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Business meals and travel expenses",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Vehicle expenses",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Payroll",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Employee benefits ",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Taxes",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>"1",
                "pay_users"=>"1"
            ],
            [
                "name" => "Business insurance",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Business licenses and permits",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Interest payments and bank fees",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Membership fees",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Professional fees and business services",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Training and education",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Refund",
                "type"=>"RFND",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Purchase of Medical Equipments",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Repair of Medical Equipments",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Hospital Repair and Maintenance",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Medicine",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Electricity Bill",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Sui Gas Bill",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Wasa Bill",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Loan / Advance Salary",
                "type"=>"",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "OTA Share",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "Surgeon fee",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Anasthatic fee",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Pediatrician",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Lab",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Assistant",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Medicine",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Miscellaneous",
                "type"=>"INPT",
                "pay_doc"=>true,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Oxygen",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>false
            ],
            [
                "name" => "PTCL Bill",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Dental Assistant Payments",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>true,
                "pay_users"=>true
            ],
            [
                "name" => "Dental Expense",
                "type"=>"",
                "pay_doc"=>false,
                "pay_others"=>false,
                "pay_users"=>false
            ]
        ]);

        $existingCategories = \App\Models\ExpenseCategory::pluck('name')->toArray();
        $array = $array->filter(function($item) use ($existingCategories) {
            return !in_array($item['name'], $existingCategories);
        });
        $records = [];
        
        foreach($array as $item){
            $records[] = \App\Models\ExpenseCategory::firstOrCreate([
                'name' => $item['name'],
            ],[
                'type' => $item['type'],
                'pay_doc' => $item['pay_doc'],
                'pay_others' => $item['pay_others'],
                'pay_users' => $item['pay_users']
            ]);
        }

        Log::info('ExpenseCategorySeeder: Created '.count($records).' new categories');

        $this->command->info('ExpenseCategorySeeder: Created '.count($records).' new categories');
    }
}
