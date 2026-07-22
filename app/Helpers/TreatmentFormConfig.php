<?php

namespace App\Helpers;

class TreatmentFormConfig
{
    /**
     * Department-level defaults for the treatment form, keyed by ServiceOrder->type.
     *
     * @var array<string, array<string, mixed>>
     */
    private const DEFAULTS = [
        'EMG' => [
            'showVitals' => true,
            'showExamFindings' => true,
            'showPrescriptions' => true,
            'showFollowUp' => true,
            'showTriage' => true,
            'requireTreatmentTime' => true,
            'showAttachments' => false,
            'showDentalChart' => false,
        ],
        'DNT' => [
            'showVitals' => false,
            'showExamFindings' => true,
            'showPrescriptions' => true,
            'showFollowUp' => true,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => false,
            'showDentalChart' => true,
        ],
        'XRAY' => [
            'showVitals' => false,
            'showExamFindings' => false,
            'showPrescriptions' => false,
            'showFollowUp' => false,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => true,
            'showDentalChart' => false,
        ],
        'ULT' => [
            'showVitals' => false,
            'showExamFindings' => false,
            'showPrescriptions' => false,
            'showFollowUp' => false,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => true,
            'showDentalChart' => false,
        ],
        'PTH' => [
            'showVitals' => false,
            'showExamFindings' => false,
            'showPrescriptions' => false,
            'showFollowUp' => false,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => true,
            'showDentalChart' => false,
        ],
        'OPD' => [
            'showVitals' => true,
            'showExamFindings' => false,
            'showPrescriptions' => true,
            'showFollowUp' => true,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => false,
            'showDentalChart' => false,
        ],
        'IND' => [
            'showVitals' => true,
            'showExamFindings' => true,
            'showPrescriptions' => true,
            'showFollowUp' => true,
            'showTriage' => false,
            'requireTreatmentTime' => false,
            'showAttachments' => false,
            'showDentalChart' => false,
        ],
    ];

    private const GENERAL_DEFAULT = [
        'showVitals' => true,
        'showExamFindings' => false,
        'showPrescriptions' => true,
        'showFollowUp' => true,
        'showTriage' => false,
        'requireTreatmentTime' => false,
        'showAttachments' => false,
        'showDentalChart' => false,
    ];

    /**
     * Department-level defaults for the given service order type.
     *
     * @return array<string, mixed>
     */
    public static function defaultsFor(string $departmentType): array
    {
        return self::DEFAULTS[$departmentType] ?? self::GENERAL_DEFAULT;
    }

    /**
     * Resolve the effective form config: department defaults, with any
     * per-service overrides layered on top.
     *
     * @param  array<string, mixed>|null  $serviceOverrides
     * @return array<string, mixed>
     */
    public static function resolve(string $departmentType, ?array $serviceOverrides): array
    {
        return array_merge(self::defaultsFor($departmentType), $serviceOverrides ?? []);
    }
}
