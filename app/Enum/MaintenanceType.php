<?php

namespace App\Enum;

enum MaintenanceType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case Calibration = 'calibration';
}
