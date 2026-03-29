<?php

namespace App\Enum;

enum AssetStatus: string
{
    case Active = 'active';
    case UnderMaintenance = 'under_maintenance';
    case Retired = 'retired';
    case Disposed = 'disposed';
}
