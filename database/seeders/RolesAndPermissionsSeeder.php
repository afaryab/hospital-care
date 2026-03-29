<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = [
            'patient',
            'transaction',
            'closing',
            'service_order',
            'expense_voucher',
            'receaveable',
            'user',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];
        $allPermissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $name = "{$resource}.{$action}";
                Permission::findOrCreate($name, 'web');
                $allPermissions[] = $name;
            }
        }

        $roles = [
            'administrator' => $allPermissions,
            'accountant' => [
                'closing.view',
                'closing.create',
                'closing.edit',
                'expense_voucher.view',
                'expense_voucher.create',
                'expense_voucher.edit',
                'receaveable.view',
                'receaveable.create',
                'receaveable.edit',
                'transaction.view',
                'patient.view',
                'service_order.view',
            ],
            'receptionist' => [
                'patient.view',
                'patient.create',
                'patient.edit',
                'transaction.view',
                'transaction.create',
                'transaction.edit',
                'closing.view',
                'closing.create',
                'service_order.view',
            ],
            'opd_doctor' => ['patient.view', 'service_order.view'],
            'ind_doctor' => ['patient.view', 'service_order.view'],
            'emergency_doctor' => ['patient.view', 'service_order.view'],
            'dentist' => ['patient.view', 'service_order.view'],
            'ultrasound_doctor' => ['patient.view', 'service_order.view'],
            'xray_technician' => ['patient.view', 'service_order.view'],
            'nursing_staff' => ['patient.view', 'service_order.view'],
            'patient_manager' => ['patient.view', 'patient.create', 'patient.edit'],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }

        User::query()->get()->each(function (User $user): void {
            $assignedRoles = [];

            if ($user->adminProfiles()->exists()) {
                $assignedRoles[] = 'administrator';
            }

            if ($user->accountantProfiles()->exists()) {
                $assignedRoles[] = 'accountant';
            }

            if ($user->receptionistProfiles()->exists()) {
                $assignedRoles[] = 'receptionist';
            }

            if ($user->opdDoctorProfiles()->exists()) {
                $assignedRoles[] = 'opd_doctor';
            }

            if ($user->indDoctorProfiles()->exists()) {
                $assignedRoles[] = 'ind_doctor';
            }

            if ($user->emergencyDoctorProfiles()->exists()) {
                $assignedRoles[] = 'emergency_doctor';
            }

            if ($user->dentistProfiles()->exists()) {
                $assignedRoles[] = 'dentist';
            }

            if ($user->ultrasoundDoctorProfiles()->exists()) {
                $assignedRoles[] = 'ultrasound_doctor';
            }

            if ($user->xrayTechnicianProfiles()->exists()) {
                $assignedRoles[] = 'xray_technician';
            }

            if ($user->nursingStaffProfiles()->exists()) {
                $assignedRoles[] = 'nursing_staff';
            }

            if ($user->patientManagerProfiles()->exists()) {
                $assignedRoles[] = 'patient_manager';
            }

            if (! empty($assignedRoles)) {
                $user->syncRoles(array_values(array_unique($assignedRoles)));
            }
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
