<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frequency → dose pattern
    |--------------------------------------------------------------------------
    |
    | How a prescription frequency code prints on patient-facing documents:
    | `pattern` is Morning + Noon + Night, `when` is the plain-English timing.
    | A null pattern prints `label` instead (e.g. QID). Keys are matched
    | case-insensitively after trimming; see App\Helpers\PrescriptionFormatter.
    |
    */

    'frequencies' => [
        'OD' => ['pattern' => [1, 0, 0], 'when' => 'Morning'],
        'QD' => ['pattern' => [1, 0, 0], 'when' => 'Morning'],
        'BD' => ['pattern' => [1, 0, 1], 'when' => 'Morning, Night'],
        'BID' => ['pattern' => [1, 0, 1], 'when' => 'Morning, Night'],
        'TDS' => ['pattern' => [1, 1, 1], 'when' => 'Morning, Noon, Night'],
        'TID' => ['pattern' => [1, 1, 1], 'when' => 'Morning, Noon, Night'],
        'HS' => ['pattern' => [0, 0, 1], 'when' => 'Night (at bedtime)'],
        'QID' => ['pattern' => null, 'label' => '4 times/day', 'when' => 'Four times a day'],
        'SOS' => ['pattern' => null, 'label' => 'When needed', 'when' => 'Only when needed'],
        'PRN' => ['pattern' => null, 'label' => 'When needed', 'when' => 'Only when needed'],
        'STAT' => ['pattern' => null, 'label' => 'Once now', 'when' => 'Immediately, once'],
    ],

    /*
    | Dosage form (Drug.type or the prescription's `form`) → printed unit.
    */
    'forms' => [
        'tablet' => 'Tablet',
        'tab' => 'Tablet',
        'capsule' => 'Capsule',
        'cap' => 'Capsule',
        'sachet' => 'Sachet',
        'syrup' => 'Teaspoon',
        'suspension' => 'Teaspoon',
        'drops' => 'Drop',
        'injection' => 'Injection',
        'inj' => 'Injection',
        'inhaler' => 'Puff',
        'cream' => 'Application',
        'ointment' => 'Application',
    ],

];
