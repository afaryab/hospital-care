<?php

namespace App\Filament\Admin\Widgets\HR;

use App\Models\Accountant;
use App\Models\Administrator;
use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\IndDoctor;
use App\Models\NursingStaff;
use App\Models\OpdDoctor;
use App\Models\Receptionist;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;
use Filament\Widgets\ChartWidget;

class StaffByRoleChart extends ChartWidget
{
    protected ?string $heading = 'Staff by Role';

    protected ?string $description = 'Current headcount per clinical and administrative role';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 3;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $roles = [
            'OPD Doctors' => OpdDoctor::count(),
            'Indoor Doctors' => IndDoctor::count(),
            'Emergency Doctors' => EmergencyDoctor::count(),
            'Dentists' => Dentist::count(),
            'Ultrasound Docs' => UltrasoundDoctor::count(),
            'X-Ray Techs' => XrayTechnician::count(),
            'Receptionists' => Receptionist::query()->distinct('user_id')->count('user_id'),
            'Nursing Staff' => NursingStaff::count(),
            'Accountants' => Accountant::count(),
            'Administrators' => Administrator::count(),
        ];

        // Remove zeroes to keep chart clean
        $roles = array_filter($roles);

        $colors = [
            'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)',
            'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)',
            'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(236, 72, 153, 0.8)',
            'rgba(6, 182, 212, 0.8)',
        ];

        return [
            'datasets' => [[
                'data' => array_values($roles),
                'backgroundColor' => array_slice($colors, 0, count($roles)),
                'borderWidth' => 2,
            ]],
            'labels' => array_keys($roles),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'right']],
        ];
    }
}
