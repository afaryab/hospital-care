@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $departmentName = $serviceOrder->service?->department?->name ?? 'Emergency';
    $tr = $serviceOrder->treatmentRecord;
    $lastVital = $tr?->vitalSigns?->last();
    $treatingDoctor = $tr?->treatingDoctor ?? $serviceOrder->doctor;
    $treatingDoctorName = $treatingDoctor?->name ?? '';
    if ($treatingDoctor?->pmdc_number) {
        $treatingDoctorName .= " (PMDC# {$treatingDoctor->pmdc_number})";
    }
    $prescriptions = $tr?->prescriptions ?? [];
    // Compact chart keeps far fewer blank alignment rows than the detailed
    // template since the whole point of this variant is fitting one page.
    $drugRowCount = count($prescriptions) > 0 ? count($prescriptions) + 1 : 3;
    $outcomeValue = $tr?->outcome?->value;
    $checked = fn (string $value) => $outcomeValue === $value ? 'X' : '';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Emergency Department - Triage Note (Compact)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        *{ box-sizing:border-box; }
        body{
            margin:0;
            font-family: Arial, Helvetica, sans-serif;
            color:#111;
            font-size: 10.5px;
        }

        .page{
            width: 100%;
            background:#fff;
            margin: 0 auto;
        }

        @media print{
            body{ background:#fff; }
            .page{ margin:0; border:0;}
        }

        .center{ text-align:center; }
        .bold{ font-weight:700; }
        .u{ text-decoration: underline; }

        .heading-wrap{
            text-align:center;
            margin-top:4px;
        }
        .heading-wrap > *{ display:block; margin: 3px auto; }
        .badge{
            display:inline-block;
            padding:2px 12px;
            background:#111;
            color:#fff;
            font-weight:700;
            letter-spacing:.5px;
            font-size: 13px;
        }
        .subhead{
            font-weight:700;
            letter-spacing:.5px;
            font-size: 12px;
        }

        .kv-table{ width:100%; border-collapse:collapse; table-layout: fixed; }
        .kv-table td{ border:0; padding: 1px 4px; vertical-align:middle; }
        .kv-label{ white-space:nowrap; font-weight:700; border-bottom: 1px dotted #222 !important; height: 14px; }
        .kv-line{ border-bottom: 1px dotted #222 !important; height: 14px; }
        .w-mid{ width: 180px; }
        .w-long{ width: 100%; }

        .section-title{
            font-weight:800;
            margin: 4px 0 2px 0;
            font-size: 11px;
        }

        .triage-note{
            text-decoration: underline;
            font-weight: 700;
        }

        .checkbox{
            display:inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #222;
            vertical-align:middle;
            margin-right:5px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            font-size: 10.5px;
        }
        td, th{
            border: 1px solid #222;
            padding: 3px 5px;
            vertical-align:top;
        }
        th{ font-weight:800; text-align:left; }

        .bg-gray{ background-color: #ebebeb; }

        .clinical-grid td{ height: 14px; }

        .drug-table th, .drug-table td{ font-size: 10px; }
        .drug-table td{ height: 14px; }

        .inline-check{
            display:inline-flex;
            align-items:center;
            gap:4px;
            margin-right:12px;
            white-space:nowrap;
            font-size: 10px;
        }
        .cb{
            width: 10px;
            height: 10px;
            border: 1px solid #222;
            display:inline-block;
        }

        .vitals-row{
            border: 1px solid #222;
            padding: 4px 6px;
            margin-top: 4px;
        }

        .no-border td{ border:0 !important; padding: 2px 0; }
    </style>
</head>
<body>
    <div class="page">

        <div class="heading-wrap">
            <div class="badge">{{ strtoupper($departmentName) }} DEPARTMENT</div>
            <div class="subhead">TRIAGE NOTE SO: <span class="u">{{ $serviceOrder->so_number ?? '' }}{{ $serviceOrder->so_short ? ' ('.$serviceOrder->so_short.')' : '' }}</span> &mdash; 1 Page</div>
        </div>

        {{-- Patient + Triage --}}
        <table style="width:100%; margin-top:6px; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:6px; border:0;">
                    <table class="kv-table">
                        <tr>
                            <td class="kv-label" style="width:26%;">MR # :</td>
                            <td class="kv-line w-mid" style="width:74%;">{{ $serviceOrder->so_number ?? '' }}{{ $serviceOrder->so_short ? ' ('.$serviceOrder->so_short.')' : '' }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Date:</td>
                            <td class="kv-line w-mid"> @hdate($serviceOrder->created_at, 'd-m-Y H:i')</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Pt. Name:</td>
                            <td class="kv-line w-long">{{ $patient->name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">S/o, D/o, W/o:</td>
                            <td class="kv-line w-long">{{ trim(($patient->relation ?? '').' '.($patient->guardian ?? '')) }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Age &amp; Sex:</td>
                            <td class="kv-line w-long">{{ $patient->age ?? '' }} / {{ $patient->gender ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%; vertical-align:top; border:0;">
                    <table class="kv-table">
                        <tr>
                            <td colspan="2" class="triage-note">Triage Note</td>
                        </tr>
                        <tr>
                            <td class="kv-label" style="width: 26%;">Time:</td>
                            <td class="kv-line w-mid" style="width: 74%;">@hdate($tr?->treated_at, 'd-m-Y H:i')</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Doctor:</td>
                            <td class="kv-line w-long">{{ $treatingDoctorName }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Triage Category:</td>
                            <td class="kv-line w-long">{{ $tr?->triage?->name }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Complain:</td>
                            <td class="kv-line w-long">{{ $tr?->chief_complaint }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Consent (single, compact) --}}
        <div class="vitals-row">
            <span class="checkbox"></span>
            <span>We give our consent according to the patient&rsquo;s condition. In case of any medication-related issue or loss of life, {{ $hospitalName }} will not be held responsible.</span>
            <table class="kv-table" style="margin-top:3px;">
                <tr>
                    <td class="kv-label" style="width:10%;">Signature:</td>
                    <td class="kv-line" style="width:40%;"></td>
                    <td class="kv-label" style="width:10%;">Relation:</td>
                    <td class="kv-line" style="width:40%;"></td>
                </tr>
            </table>
        </div>

        {{-- History + Vitals --}}
        <table class="clinical-grid" style="margin-top:4px;">
            <tr>
                <td class="bold" style="width:50%;">HISTORY OF PRESENT ILLNESS:</td>
                <td class="bold" style="width:50%;">EXAMINATION / VITALS:</td>
            </tr>
            <tr>
                <td>{{ $tr?->history_of_present_illness }}</td>
                <td>
                    Airway: Clear Y/N &nbsp; Breathing: Spontaneous Y/N<br>
                    R/R: {{ $lastVital?->respiratory_rate }} /min &nbsp; Pulse: {{ $lastVital?->pulse_rate }} /min<br>
                    BP: {{ $lastVital && $lastVital->blood_pressure_systolic ? $lastVital->blood_pressure_systolic.'/'.$lastVital->blood_pressure_diastolic : '' }} &nbsp; Temp: {{ $lastVital?->temperature }} F &nbsp; GCS: &nbsp; BSL:
                </td>
            </tr>
            <tr>
                <td class="bold">
                    @php $examFindings = $tr?->examination_findings ?? []; @endphp
                    @foreach($examFindings as $label => $value)
                        {{ $label }}: {{ $value }}<br>
                    @endforeach
                </td>
                <td>
                    <span class="inline-check"><span class="cb"></span> Allergies</span>
                    <span class="inline-check"><span class="cb"></span> Immunization</span>
                    <span class="inline-check"><span class="cb"></span> Smoker</span>
                    <span class="inline-check"><span class="cb"></span> Medications</span>
                </td>
            </tr>
        </table>

        {{-- Treatment / Investigation --}}
        <table class="clinical-grid" style="margin-top:4px;">
            <tr>
                <td class="bold" style="width:60%;">TREATMENT GIVEN:</td>
                <td class="bold" style="width:40%;">INVESTIGATION DONE:</td>
            </tr>
            <tr>
                <td style="height:24px;">{{ $tr?->treatment_plan }}</td>
                <td style="height:24px;"></td>
            </tr>
        </table>

        {{-- Drug chart --}}
        <table class="drug-table" style="margin-top:4px;">
            <tr>
                <th class="bold bg-gray" style="width:12%;">DATE</th>
                <th class="bold bg-gray" style="width:40%;">DRUG / DOSE / ROUTE</th>
                <th class="bold bg-gray" style="width:24%;">PRESCRIBED BY</th>
                <th class="bold bg-gray" style="width:12%;">TIME</th>
                <th class="bold bg-gray" style="width:12%;">SIGN</th>
            </tr>

            @for($i=0; $i<$drugRowCount; $i++)
                @php
                    $rx = $prescriptions[$i] ?? null;
                    $rxTime = $rx['given_at'] ?? $tr?->treated_at;
                @endphp
                <tr>
                    <td>{{ $rx ? \App\Helpers\DateHelper::pdfFormat($rxTime, 'd-m-Y') : '' }}</td>
                    <td>
                        @if($rx)
                            {{ trim(($rx['drug_name'] ?? '').' '.($rx['dose'] ?? '').' '.($rx['frequency'] ?? '').' '.($rx['route'] ?? '')) }}
                        @endif
                    </td>
                    <td>{{ $rx ? $treatingDoctorName : '' }}</td>
                    <td>{{ $rx ? \App\Helpers\DateHelper::pdfFormat($rxTime, 'H:i') : '' }}</td>
                    <td></td>
                </tr>
            @endfor
        </table>

        {{-- Diagnosis + Disposition --}}
        <table class="clinical-grid" style="margin-top:4px;">
            <tr>
                <th class="bold bg-gray">DISCHARGE DIAGNOSIS</th>
            </tr>
            <tr>
                <td>{{ trim(($tr?->diagnosis_code ?? '').' '.($tr?->diagnosis_text ?? '')) }}</td>
            </tr>
        </table>

        <div class="vitals-row" style="margin-top:4px;">
            <span class="inline-check"><span class="cb">{{ $checked('discharged') }}</span> DISCHARGED HOME &nbsp;{{ $outcomeValue === 'discharged' ? '('.\App\Helpers\DateHelper::pdfFormat($tr?->outcome_at, 'd-m-Y H:i').')' : '' }}</span>
            <span class="inline-check"><span class="cb">{{ $checked('referred') }}</span> REFERRED / TRANSFERRED TO: {{ $outcomeValue === 'referred' ? $tr?->referral_to : '' }}</span>
            <span class="inline-check"><span class="cb"></span> ADMITTED TO:&nbsp;</span>
            <span class="inline-check"><span class="cb">{{ $checked('expired') }}</span> DIED IN ED &nbsp;{{ $outcomeValue === 'expired' ? '('.\App\Helpers\DateHelper::pdfFormat($tr?->outcome_at, 'd-m-Y H:i').')' : '' }}</span>
            <span class="inline-check"><span class="cb"></span> LEFT AGAINST MEDICAL ADVICE</span>
            <div style="margin-top:3px;">{{ $tr?->outcome_notes }}</div>
        </div>

        {{-- Signatures --}}
        <table class="no-border" style="margin-top:8px;">
            <tr>
                <td style="width:50%;">
                    <table class="kv-table">
                        <tr>
                            <td class="kv-label" style="width:40%;">Discharging Doctor:</td>
                            <td class="kv-line w-long">{{ $treatingDoctorName }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table class="kv-table">
                        <tr>
                            <td class="kv-label" style="width:30%;">Nurse Sign:</td>
                            <td class="kv-line w-long"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
