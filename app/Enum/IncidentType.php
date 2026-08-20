<?php

namespace App\Enum;

enum IncidentType: string
{
    // Automated security-detection types (App\Services\BreachDetectionService)
    case FailedLoginThreshold = 'failed_login_threshold';
    case NewLoginContext = 'new_login_context';
    case BulkPatientAccess = 'bulk_patient_access';

    // Manually-reportable types (PHC guideline §9.1)
    case ClinicalError = 'clinical_error';
    case SystemFailure = 'system_failure';
    case DataBreach = 'data_breach';
    case DelayInTreatment = 'delay_in_treatment';

    public function label(): string
    {
        return match ($this) {
            self::FailedLoginThreshold => 'Failed Login Threshold',
            self::NewLoginContext => 'New Login Context',
            self::BulkPatientAccess => 'Bulk Patient Access',
            self::ClinicalError => 'Clinical Error',
            self::SystemFailure => 'System Failure',
            self::DataBreach => 'Data Breach',
            self::DelayInTreatment => 'Delay in Treatment',
        };
    }

    /**
     * The automated security-detection types are only ever written by
     * BreachDetectionService — these are the options a human filing a
     * manual report should actually be offered.
     *
     * @return array<self>
     */
    public static function manuallyReportable(): array
    {
        return [self::ClinicalError, self::SystemFailure, self::DataBreach, self::DelayInTreatment];
    }
}
