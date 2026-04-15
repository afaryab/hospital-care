<?php

namespace Database\Seeders;

use App\Models\HospitalSetting;
use Illuminate\Database\Seeder;

class HospitalSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'hospital_name' => config('app.name', 'Hospital Care'),
            'hospital_logo_path' => '',
            'hospital_address' => '',
            'hospital_phone' => '',
            'hospital_email' => '',
            'hospital_ntn' => '',
            'hospital_strn' => '',
            'hospital_website' => '',
            'abacus_auto_map_accounts' => false,
        ];

        foreach ($defaults as $key => $value) {
            HospitalSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
