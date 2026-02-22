<?php

namespace Database\Seeders;

use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\IndDoctor;
use App\Models\NursingStaff;
use App\Models\OpdDoctor;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServicesAndDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createdByUserId = User::query()->value('id');

        if (!$createdByUserId) {
            $createdByUserId = User::query()->firstOrCreate(
                ['email' => 'system-seeder@hospital-care.local'],
                [
                    'name' => 'System Seeder',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ]
            )->id;
        }

                
        $opdDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'OPD',
        ], [
            'name' => 'OPD',
            'slug' => 'OPD',
            'image' => '/img/opd.png',
            'have_composit_services' => false,
        ]);

        $indoorDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'IND',
        ], [
            'name' => 'Indoor',
            'slug' => 'IND',
            'image' => '/img/ind.png',
            'have_composit_services' => true,
        ]);

        $emergencyDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'EMG',
        ], [
            'name' => 'Emergency',
            'slug' => 'EMG',
            'image' => '/img/emergency.png',
            'have_composit_services' => false,
        ]);

        $dentalDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'DNT',
        ], [
            'name' => 'Dental',
            'slug' => 'DNT',
            'image' => '/img/dental.png',
            'have_composit_services' => false,
        ]);

        ServiceDepartment::firstOrCreate([
            'slug' => 'PTH',
        ], [
            'name' => 'Laboratory',
            'slug' => 'PTH',
            'image' => '/img/laboratory.png',
            'have_composit_services' => false,
        ]);

        $ultrasoundDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'ULT',
        ], [
            'name' => 'Ultrasound',
            'slug' => 'ULT',
            'image' => '/img/ultrasound.png',
            'have_composit_services' => false,
        ]);

        $radiologyDepartment = ServiceDepartment::firstOrCreate([
            'slug' => 'XRAY',
        ], [
            'name' => 'Xray',
            'slug' => 'XRAY',
            'image' => '/img/xray.png',
            'have_composit_services' => false,
        ]);

        $services = collect([
            [
                "slug"=>"M.O_MOR",
                "name"=>"M.O Morning",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"M.O_EV",
                "name"=>"M.O Evening",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"M.O_NT",
                "name"=>"M.O Night",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"W.M.O_MOR",
                "name"=>"W.M.O Morning",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"400",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"W.M.O_EV",
                "name"=>"W.M.O Evening",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"400",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"W.M.O_NT",
                "name"=>"W.M.O Night",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"400",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"CONST",
                "name"=>"Consultation",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"400",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class   
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Ultrasound",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1300",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Physiotherapy",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"5000",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class   
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"DENTL",
                "name"=>"Dental Associate",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1500",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Dermatolgy",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class   
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Health Card Service",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Endoscopy",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"3",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"col",
                "name"=>"Colonocopy",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class   
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"gast",
                "name"=>"Gastroscopy",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"Cotrl",
                "name"=>"Cotaryl",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"1",
                "service_provider_types"=> [
                    OpdDoctor::class   
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Paeds M.O Morning",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Paeds M.O Evening ",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Paeds M.O Night",
                "service_department_id"=> $opdDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"NRML_DLV_G",
                "name"=>"Normal Delivery Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"25000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"NRML_DLV_P",
                "name"=>"Normal Delivery Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"28000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"NRML_DLV_V",
                "name"=>"Normal Delivery Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"32000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"DNC_G",
                "name"=>"DNC Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"25000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"DNC_P",
                "name"=>"DNC Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"28000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"DNC_V",
                "name"=>"DNC Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"32000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"EXP_G",
                "name"=>"Expulsion Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"25000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"EXP_P",
                "name"=>"Expulsion Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"30000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"EXP_V",
                "name"=>"Expulsion Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"35000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"C_SEC_G",
                "name"=>"C-Section Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"48000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"C_SEC_P",
                "name"=>"C-Section Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"C_SEC_V",
                "name"=>"C-Section Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_G",
                "name"=>"Hystrectomy Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_P",
                "name"=>"Hystrectomy Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_V",
                "name"=>"Hystrectomy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OV_CYSY_G",
                "name"=>"Ovarian Cyst General",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OV_CYSY_P",
                "name"=>"Ovarian Cyst Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OV_CYSY_V",
                "name"=>"Ovarian Cyst Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"APNDX_G",
                "name"=>"Apendix Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"45000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"APNDX_P",
                "name"=>"Apendix Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"APNDX_V",
                "name"=>"Apendix Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OPN_MTHD_G",
                "name"=>"Open Method Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OPN_MTHD_P",
                "name"=>"Open Method Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"OPN_MTHD_V",
                "name"=>"Open Method Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_G",
                "name"=>"Laproscopic Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"85000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_P",
                "name"=>"Laproscopic Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"90000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_V",
                "name"=>"Laproscopic Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"95000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TVP_G",
                "name"=>"TVP Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TVP_P",
                "name"=>"TVP Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"80000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TVP_V",
                "name"=>"TVP Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"90000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TURP_G",
                "name"=>"TURP Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"95000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TURP_P",
                "name"=>"TURP Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"100000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TURP_V",
                "name"=>"TURP Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"110000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"PIL_G",
                "name"=>"Piles Operation Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"PIL_P",
                "name"=>"Piles Operation Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"PIL_V",
                "name"=>"Piles Operation Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ABD_G",
                "name"=>"Abdominal General Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ABD_P",
                "name"=>"Abdominal Private Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ABD_V",
                "name"=>"Abdominal Vip Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ING_G",
                "name"=>"Inguinal Gereral Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ING_P",
                "name"=>"Inguinal Private Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ING_V",
                "name"=>"Inguinal Vip Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"INC_G",
                "name"=>"Incisional Gereral Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"INC_P",
                "name"=>"Incisional Private Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"80000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"INC_V",
                "name"=>"Incisional Vip Hernia",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"90000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"GOTRS_G",
                "name"=>"Goitres Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"100000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"GOTRS_P",
                "name"=>"Goitres Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"110000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"GOTRS_V",
                "name"=>"Goitres Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"120000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"BLDR_STN_G",
                "name"=>"Bladder Stones Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"BLDR_STN_P",
                "name"=>"Bladder Stones Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"BLDR_STN_V",
                "name"=>"Bladder Stones Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"75000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TNSL_G",
                "name"=>"Tonsils Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TNSL_P",
                "name"=>"Tonsils Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"TNSL_V",
                "name"=>"Tonsils Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"NOS_OPR_G",
                "name"=>"ENT Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"NOS_OPR_P",
                "name"=>"ENT Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"NOS_OPR_V",
                "name"=>"ENT Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HRNIA_G",
                "name"=>"Hernia Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HRNIA_P",
                "name"=>"Hernia Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HRNIA_V",
                "name"=>"Hernia Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"URLG_G",
                "name"=>"Uralogy Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"URLG_P",
                "name"=>"Uralogy Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"55000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"URLG_V",
                "name"=>"Uralogy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ORTHO_G",
                "name"=>"Ortho Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ORTHO_P",
                "name"=>"Ortho Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"75000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ORTHO_V",
                "name"=>"Ortho Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"80000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ENT_G",
                "name"=>"E&T Gereral",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ENT_P",
                "name"=>"E&T Private",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"75000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"ENT_V",
                "name"=>"E&T Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"80000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"",
                "name"=>"Local",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"10000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"",
                "name"=>"Conservative",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"10000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"",
                "name"=>"Circumcision",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"7000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_G",
                "name"=>"Laparotomy General",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_P",
                "name"=>"Laparotomy Pvt",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"LAP_VIP",
                "name"=>"Laparotomy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"MYO_G",
                "name"=>"Myomectomy Gernal",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"MYO_P",
                "name"=>"Myomectomy Pvt",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"MYO_VIP",
                "name"=>"Myomectomy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"CYS_G",
                "name"=>"Cystectomy General",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"CYS_P",
                "name"=>"Cystectomy Pvt",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"CYS_VIP",
                "name"=>"Cystectomy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_G",
                "name"=>"Hysterotomy General",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"60000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_P",
                "name"=>"Hysterotomy Pvt",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"65000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"HYS_VIP",
                "name"=>"Hysterotomy Vip",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"70000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"",
                "name"=>"Paeds Conservative",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"10000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"DGA",
                "name"=>"Dental G/A Case",
                "service_department_id"=>$indoorDepartment->id,
                "charges"=>"50000",
                "have_service_provider"=>"1",
                "service_provider_types" => [
                    IndDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug"=>"DRIP",
                "name"=>"Drip Service",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"VENOFER",
                "name"=>"Venofer Service",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"SHRT_STAY_12H",
                "name"=>"Short Stay for 12 Hrs",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"2000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"SHRT_STAY_24H",
                "name"=>"Short Stay for 24 Hrs",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"3000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"MJR_DRSNG",
                "name"=>"Major Dressing",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"MNR_DRSNG",
                "name"=>"Minor Dressing",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"150",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"NBL",
                "name"=>"Nebulization",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"100",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"INJ",
                "name"=>"Injection",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"100",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"BLD_SUGR",
                "name"=>"Blood Sugar Check",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"100",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"ICU",
                "name"=>"ICU",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"6000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"FOL_CATH",
                "name"=>"Folyes Catheter",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"1200",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"NG_TUB_PAS",
                "name"=>"NG Tube Passing",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"1200",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"STMCH_WASH",
                "name"=>"Stomach Wash",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"5000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"CTG",
                "name"=>"CTG",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"600",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"ECG",
                "name"=>"ECG",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"BLD_TRNS",
                "name"=>"Blood Transfusion",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"1500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"BP",
                "name"=>"Blood Pressure",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"50",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"STCHS",
                "name"=>"Stitches",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"X_RAY",
                "name"=>"X-Ray",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"1500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"BED_P_H",
                "name"=>"Bed (per hour)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"200",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"OXG_P_H",
                "name"=>"Oxygen (per hour)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"200",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"VAC",
                "name"=>"Vaccination",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"SEM_PVT_RM",
                "name"=>"Semi Private Room",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"5000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"PVT_RM",
                "name"=>"Private Room",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"7000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"VIP_RM",
                "name"=>"Vip Room",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"9000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"NURSRY",
                "name"=>"Nursery",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"6000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"others",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"500",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Circumcision",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"5000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Other",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"7000",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"EEG",
                "name"=>"EEG",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"ECHO",
                "name"=>"Echocardiogram",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Dental Consultation",
                "service_department_id"=>$dentalDepartment->id,
                "charges"=>"1000",
                "have_service_provider"=>"0",
                "service_provider_types" => [
                    Dentist::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"",
                "name"=>"Hydra Facial",
                "service_department_id"=>$dentalDepartment->id,
                "charges"=>"4000",
                "have_service_provider"=>"0",
                "service_provider_types" => [
                    Dentist::class
                ],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"ULT",
                "name"=>"Ultrasound",
                "service_department_id"=>$radiologyDepartment->id,
                "charges"=>"1300",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug"=>"ECHO",
                "name"=>"Echocardiogram",
                "service_department_id"=>$radiologyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>"0",
                "service_provider_types"=>[],
                "is_composit_service"=>"0"
            ],
            [
                "slug" => "EMR-RED",
                "name" => "Immediate Resuscitation (Red)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug" => "EMR-YELLOW",
                "name" => "Emergency (Yellow)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug" => "EMR-BLUE",
                "name" => "Urgent (Blue)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug" => "EMR-SKY",
                "name" => "Semi Urgent (Sky Blue)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug" => "EMR-SKY",
                "name" => "Semi Urgent (Sky Blue)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ],
            [
                "slug" => "EMR-GREEN",
                "name" => "Non Urgent (Green)",
                "service_department_id"=>$emergencyDepartment->id,
                "charges"=>"0",
                "have_service_provider"=>1,
                "service_provider_types"=>[
                    EmergencyDoctor::class
                ],
                "is_composit_service"=>"1"
            ]
        ]);

        $services = $services->map(function($service){
            if(empty($service["slug"])){
                $service["slug"] = strtoupper(Str::slug($service["name"], '_'));
            }
            return $service;
        });

        $existingSlugs = Service::pluck("slug")->toArray();

        $services = $services->filter(function($service) use ($existingSlugs){
            return !in_array($service["slug"], $existingSlugs);
        });

        $records = [];

        foreach ($services as $service) {
            
            $records[] = Service::firstOrCreate([
                "slug"=>$service["slug"],
                "service_department_id"=>$service["service_department_id"],
            ],[
                "name"=>$service["name"],
                "charges"=>$service["charges"],
                "charges_include_tax" => 1,
                "tax_rate" => 0,
                "created_by" => $createdByUserId,
                "have_service_provider"=>$service["have_service_provider"],
                "is_composit_service"=>$service["is_composit_service"],
                "service_provider_types"=>$service["service_provider_types"],
            ]);
        }

        Log::info("Seeded ".count($records)." services.");

        $this->command->info("Seeded ".count($records)." services.");
    }
}
