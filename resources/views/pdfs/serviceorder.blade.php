{{-- resources/views/forms/emergency-clinical-performa.blade.php --}}
@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $departmentName = $serviceOrder->service?->department?->name ?? 'Emergency';
    $pastDiagnoses = $pastDiagnoses ?? collect();
    $tr = $serviceOrder->treatmentRecord;
    $lastVital = $tr?->vitalSigns?->last();
    $treatingDoctor = $tr?->treatingDoctor ?? $serviceOrder->doctor;
    $treatingDoctorName = $treatingDoctor?->name ?? '';
    if ($treatingDoctor?->pmdc_number) {
        $treatingDoctorName .= " (PMDC# {$treatingDoctor->pmdc_number})";
    }
    $prescriptions = $tr?->prescriptions ?? [];
    // Blank forms keep a full set of writing lines for alignment when printed
    // before any treatment is recorded; once real entries exist, only pad
    // with one trailing blank row instead of filling out to the minimum.
    $drugRowCount = count($prescriptions) > 0 ? count($prescriptions) + 1 : 5;
    $treatmentGivenRowCount = $tr?->treatment_plan ? 2 : 7;
    $outcomeValue = $tr?->outcome?->value;
    $checked = fn (string $value) => $outcomeValue === $value ? 'X' : '';
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Emergency Department - Clinical Performa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        /* Fonts are registered via mPDF config; omit @font-face to prevent parser issues */
        /* Remove CSS variables for mPDF compatibility */

        *{ box-sizing:border-box; }
        body{
            margin:0;
            font-family: Arial, Helvetica, sans-serif;
            color:#111;
        }

        /* Page container (no forced min-height) */
        .page{
            width: 100%;
            background:#fff;
            margin: 0 auto;
        }

        @media print{
            body{ background:#fff; }
            .page{ margin:0; border:0;}
        }
        .bottom-rule{
            border-bottom: 2px solid #222;
            margin: 12px 0 0 0;
        }

        .center{ text-align:center; }
        .right{ text-align:right; }
        .bold{ font-weight:700; }
        .u{ text-decoration: underline; }
        .small{ font-size: 11px; }

        .heading-wrap{
            text-align:center;
            margin-top:6px;
        }
        .heading-wrap > *{ display:block; margin: 4px auto; }
        .badge{
            display:inline-block;
            padding:3px 14px;
            background:#111;
            color:#fff;
            font-weight:700;
            letter-spacing:.5px;
            font-size: 14px;
        }
        .subhead{
            font-weight:700;
            letter-spacing:.6px;
            font-size: 14px;
        }

        /* Table-based layout helpers */
        .kv-table{ width:100%; border-collapse:collapse; table-layout: fixed; }
        .kv-table td{ border:0; padding: 2px 4px; vertical-align:middle; }
        .kv-label{ white-space:nowrap; font-weight:700; border-bottom: 1px dotted #222 !important; height: 16px; }
        .kv-line{ border-bottom: 1px dotted #222 !important; height: 16px; }
        .w-short{ width: 110px; }
        .w-mid{ width: 180px; }
        .w-long{ width: 100%; }

        .box{
            border: 2px solid #222;
            padding: 6px;
        }

        .row{
            display:flex;
            gap:10px;
            align-items:flex-end;
            margin: 6px 0;
        }
        .row > .grow{ flex:1; }

        .label{
            font-weight:700;
            white-space:nowrap;
        }

        .line{
            flex:1;
            border-bottom: 1px solid #222;
            height: 16px;
        }

        .line.short{ max-width:140px; }
        .line.mid{ max-width:220px; }
        .line.long{ max-width:100%; }

        .triage-title{
            font-weight:800;
            text-align:center;
            text-decoration: underline;
        }

        .consent-row{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:10px;
            margin-top: 8px;
        }

        .consent{
            border: 1px solid transparent;
            padding: 6px 8px;
        }

        .checkbox{
            display:inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #222;
            vertical-align:middle;
            margin-right:6px;
        }
        

        /* Removed Urdu-specific styling */

        table{
            width:100%;
            border-collapse:collapse;
            font-size: 12px;
        }
        td, th{
            border: 1px solid #222;
            padding: 6px;
            vertical-align:top;
        }
        th{ font-weight:800; text-align:left;  }

        .thin-top{ border-top: 1px solid #222; }
        .no-border{ border:0 !important; }

        .section-title{
            font-weight:800;
            margin: 8px 0 6px 0;
        }

        /* Big clinical grid (page 1) */
        .clinical-grid{
            margin-top: 8px;
        }
        .clinical-grid td{
            height: 18px;
        }

        /* right side checklist column */
        .side-col{
            width: 28%;
        }
        .main-col{
            width: 72%;
        }
        .bg-gray{
            background-color: #ebebeb;
        }

        /* Page 2 */
        .drug-table th , .discharge-table th{
            border-top: 0px;
        }
        .drug-table th, .drug-table td{ font-size: 11.5px; }
        .drug-table td{ height: 18px; }
        .discharge-table td{ height: 18px; }

        .inline-check{
            display:inline-flex;
            align-items:center;
            gap:6px;
            margin-right:14px;
            white-space:nowrap;
            font-size: 11.5px;
        }
        .cb{
            width: 12px;
            height: 12px;
            border: var(--thin) solid var(--line);
            display:inline-block;
        }

        .footer-sign{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:10px;
            align-items:end;
            margin-top: 10px;
        }

        .sig-line{
            border-bottom: 1px solid #222;
            height: 18px;
        }
        .d-block{ display:block; }
        .text-center{ text-align:center; }
        .triage-note{
            text-decoration: underline;
        }
        .table-container{
            padding:0;
        }
        .border-none td, .no-border{
            border:0 !important;
        }
    </style>
</head>
<body>
    <div class="page">

        <div class="heading-wrap">
            <div class="badge d-block text-center">{{ strtoupper($departmentName) }} DEPARTMENT</div>
            <div class="subhead">CLINICAL PERFORMA SO: <span class="u">{{ $serviceOrder->so_number ?? '' }}{{ $serviceOrder->so_short ? ' ('.$serviceOrder->so_short.')' : '' }}</span> Page 1/2</div>
        </div>

        <table style="width:100%; margin-top:12px; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:3px;">
                    <table class="kv-table">
                        <tr>
                            <td class="kv-label" style="width:30%;">MR # :</td>
                            <td class="kv-line w-mid" style="width:70%;">{{ $serviceOrder->so_number ?? '' }}{{ $serviceOrder->so_short ? ' ('.$serviceOrder->so_short.')' : '' }}</td>
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
                        <tr>
                            <td class="kv-label">Address:</td>
                            <td class="kv-line w-long">{{ $patient->address ?? '' }}</td>
                        </tr>
                        <tr>
                            <td  class="kv-line w-long" colspan="2" ></td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <table class="kv-table">
                        <tr>
                            <td colspan="2" class="triage-note" >Triage Note</td>
                        </tr>
                        <tr>
                            <td class="kv-label" style="width: 30%;">Time:</td>
                            <td class="kv-line w-mid" style="width: 70%;">@hdate($tr?->treated_at, 'd-m-Y H:i')</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Name of Doctor:</td>
                            <td class="kv-line w-long">{{ $treatingDoctorName }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Name of Nurse:</td>
                            <td class="kv-line w-long"></td>
                        </tr>
                        <tr>
                            <td class="kv-label">Triage Category:</td>
                            <td class="kv-line w-long">{{ $tr?->triage?->name }}</td>
                        </tr>
                        <tr>
                            <td class="kv-label">Complain:</td>
                            <td class="kv-line w-long">{{ $tr?->chief_complaint }}</td>
                        </tr>
                        <tr>
                            <td  class="kv-line w-long" colspan="2" ></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Urdu consent lines + checkboxes --}}
        <table style="width:100%; margin-top:8px; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:8px;">
                    <div class="consent">
                        <div>
                            <span class="checkbox"></span>
                            <span>We give our consent according to the patient’s condition. In case of any medication-related issue or loss of life, {{ $hospitalName }} will not be held responsible.</span>
                        </div>
                        <table class="kv-table" style="margin-top:6px;">
                            <tr>
                                <td class="label">Signature:</td>
                                <td class="label" style="padding-left:8px;">Relation:</td>
                            </tr>
                            <tr>
                                <td class="kv-line w-long"></td>
                                <td class="kv-line w-mid"></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <div class="consent">
                        <div>
                            <span class="checkbox"></span>
                            <span>We give our consent according to the patient’s condition. In case of any medication-related issue or loss of life, {{ $hospitalName }} will not be held responsible.</span>
                        </div>
                        <table class="kv-table" style="margin-top:6px;">
                            <tr>
                                <td class="label">Signature:</td>
                                <td class="label" style="padding-left:8px;">Relation:</td>
                            </tr>
                            <tr>
                                <td class="kv-line w-long"></td>
                                <td class="kv-line w-mid"></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Main clinical table --}}
        @php $examFindings = $tr?->examination_findings ?? []; @endphp
        <table class="clinical-grid" style="margin-top:8px;">
            <tr>
                <td style="width:70%;" class="bold">DOCTOR'S NAME: {{ $treatingDoctorName }} <span style="float:right">TIME SEEN BY DOCTOR:</span></td>
                <td style="width:30%;" class="bold center">@hdate($tr?->treated_at, 'H:i')</td>
            </tr>
            <tr>
                <td class="bold">HISTORY:</td>
                <td class="bold">PAST HISTORY:</td>
            </tr>

            {{-- history lines: left = this visit's HPI, right = last 6 diagnosed conditions from previous visits --}}
            @php $historyRowCount = max(4, $pastDiagnoses->count()); @endphp
            @for($i=0; $i<$historyRowCount; $i++)
                @php $pastDx = $pastDiagnoses[$i] ?? null; @endphp
                <tr>
                    <td>{{ $i === 0 ? $tr?->history_of_present_illness : '' }}</td>
                    <td>
                        @if($pastDx)
                            {{ $pastDx->icd10Code?->code ?? $pastDx->diagnosis_code }}{{ $pastDx->icd10Code?->description ? ' - '.$pastDx->icd10Code->description : '' }}
                        @endif
                    </td>
                </tr>
            @endfor

            {{-- Examination rows --}}
            <tr>
                <td class="bold">
                    EXAMINATION: &gt; Airway: Clear&nbsp; Y&nbsp; N
                    &nbsp;&nbsp;&gt; Breathing: Spontaneous&nbsp; Y&nbsp; N
                    &nbsp;&nbsp;&gt; R/R:&nbsp;{{ $lastVital?->respiratory_rate }}&nbsp; /min
                    &nbsp;&nbsp;&gt; GCS
                </td>
                <td style="height:24px;"></td>
            </tr>

            <tr>
                <td class="bold">
                    CIRCULATIONS:&nbsp; Y&nbsp; N
                    &nbsp;&nbsp;&gt; Pulse:&nbsp;{{ $lastVital?->pulse_rate }}&nbsp; /min
                    &nbsp;&nbsp;&gt; BP&nbsp;{{ $lastVital && $lastVital->blood_pressure_systolic ? $lastVital->blood_pressure_systolic.'/'.$lastVital->blood_pressure_diastolic : '' }}
                    &nbsp;&nbsp;&gt; TEMP:&nbsp;{{ $lastVital?->temperature }}&nbsp; F
                    &nbsp;&nbsp;&gt; BSL
                </td>
                <td style="height:24px;"></td>
            </tr>
            <tr>
                <td></td>
                <td class="bold">MEDICATIONS&nbsp; Y&nbsp; N</td>
            </tr>

            {{-- big writing area + right column labels --}}
            @php $examEntries = array_values($examFindings); $examLabels = array_keys($examFindings); @endphp
            @for($i=0; $i<10; $i++)
                <tr>
                    <td>
                        @if(isset($examLabels[$i]))
                            <span class="bold">{{ $examLabels[$i] }}:</span> {{ $examEntries[$i] }}
                        @endif
                    </td>
                    <td class="bold">
                        @if($i==2) <span class="bold">ALLERGIES:&nbsp; Y&nbsp; N</span> @endif
                        @if($i==5) <span class="bold">IMMUNIZATION:&nbsp; Y&nbsp; N</span> @endif
                        @if($i==8) <span class="bold">SMOKER:&nbsp; Y&nbsp; N</span> @endif
                    </td>
                </tr>
            @endfor
            <tr>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <div style="page-break-after: always;"></div>
    <div class="page last">

        <div class="heading-wrap">
            <div class="badge d-block text-center">{{ strtoupper($departmentName) }} DEPARTMENT</div>
            <div class="subhead">CLINICAL PERFORMA SO: <span class="u">{{ $serviceOrder->so_number ?? '' }}</span> Page 2/2</div>
        </div>

        {{-- Treatment / Investigation --}}
        <table class="clinical-grid">
            <tr>
                <td class="bold" style="width:70%;">TREATMENT GIVEN:</td>
                <td class="bold" style="width:30%;">INVESTIGATION DONE:</td>
            </tr>

            @for($i=0; $i<$treatmentGivenRowCount; $i++)
                <tr>
                    <td>{{ $i === 0 ? $tr?->treatment_plan : '' }}</td>
                    <td></td>
                </tr>
            @endfor
        </table>

        {{-- Drug chart --}}
        <table class="drug-table">
            <tr>
                <th class="bold bg-gray" style="width:10%;">DATE</th>
                <th class="bold bg-gray" style="width:12%;">DRUG</th>
                <th class="bold bg-gray" style="width:12%;">DOSE</th>
                <th class="bold bg-gray" style="width:12%;">ROUTE</th>
                <th class="bold bg-gray" style="width:16%;">PRESCRIBED BY</th>
                <th class="bold bg-gray" style="width:22%;">GIVEN BY SIGN</th>
                <th class="bold bg-gray" style="width:12%;">TIME</th>
            </tr>

            @for($i=0; $i<$drugRowCount; $i++)
                @php
                    $rx = $prescriptions[$i] ?? null;
                    $rxTime = $rx['given_at'] ?? $tr?->treated_at;
                @endphp
                <tr>
                    <td>{{ $rx ? \App\Helpers\DateHelper::pdfFormat($rxTime, 'd-m-Y') : '' }}</td>
                    <td colspan="3">
                        @if($rx)
                            {{ trim(($rx['drug_name'] ?? '').' '.($rx['dose'] ?? '').' '.($rx['frequency'] ?? '').' '.($rx['route'] ?? '')) }}
                        @endif
                    </td>
                    <td>{{ $rx ? $treatingDoctorName : '' }}</td>
                    <td></td>
                    <td>{{ $rx ? \App\Helpers\DateHelper::pdfFormat($rxTime, 'H:i') : '' }}</td>
                </tr>
            @endfor
        </table>

        {{-- Discharge + Nursing --}}
        <table class="discharge-table">
            <tr>
                <th class="bold bg-gray" style="width:70%;">DISCHARGE DIAGNOSIS</th>
                <th class="bold bg-gray" style="width:30%;">NURSING INTERVENTIONS</th>
            </tr>
            <tr>
                <td rowspan="3">{{ trim(($tr?->diagnosis_code ?? '').' '.($tr?->diagnosis_text ?? '')) }}</td>
                <td></td>
            </tr>

            @for($i=0; $i<2; $i++)
                <tr>
                    <td></td>
                </tr>
            @endfor
            <tr>
                <td>
                    <div class="bold"><span class="inline-check"><span class="cb">{{ $checked('discharged') }}</span>  DISCHARGE PLAN</div>
                    <td>{{ $tr?->outcome_notes }}</td>
                </td>
            </tr>
            <tr>
                <td class="bold"><span class="inline-check"><span class="cb">{{ $checked('referred') }}</span> REFERRED TO:&nbsp; Surgical, Medical, O &amp; G, Paeds, Other: {{ $tr?->referral_to }}</span></td>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container">
                    <table class="border-none">
                        <tr>
                            <td style="width: 43%;"><span class="inline-check"><span class="cb"></span> ADMITTED TO:&nbsp;</span></td>
                            <td style="width: 20%;"><span class="inline-check"><span class="cb"></span> DATE:&nbsp;</span></td>
                            <td style="width: 27%;"><span class="inline-check"><span class="cb"></span> TIME:&nbsp;</span></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container">
                    <table class="border-none">
                        <tr>
                            <td style="width: 43%;"><span class="inline-check"><span class="cb">{{ $checked('referred') }}</span> TRANSFERRED TO:&nbsp;{{ $outcomeValue === 'referred' ? $tr?->referral_to : '' }}</span></td>
                            <td style="width: 20%;"><span class="inline-check"><span class="cb"></span> DATE:&nbsp;{{ $outcomeValue === 'referred' ? $tr?->outcome_at?->format('d-m-Y') : '' }}</span></td>
                            <td style="width: 27%;"><span class="inline-check"><span class="cb"></span> TIME:&nbsp;{{ $outcomeValue === 'referred' ? $tr?->outcome_at?->format('H:i') : '' }}</span></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container">
                    <table class="border-none">
                        <tr>
                            <td style="width: 43%;"><span class="inline-check"><span class="cb">{{ $checked('discharged') }}</span> DISCHARGED HOME:&nbsp;</span></td>
                            <td style="width: 20%;"><span class="inline-check"><span class="cb"></span> DATE:&nbsp;{{ $outcomeValue === 'discharged' ? $tr?->outcome_at?->format('d-m-Y') : '' }}</span></td>
                            <td style="width: 27%;"><span class="inline-check"><span class="cb"></span> TIME:&nbsp;{{ $outcomeValue === 'discharged' ? $tr?->outcome_at?->format('H:i') : '' }}</span></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container">
                    <table class="border-none">
                        <tr>
                            <td style="width: 43%;"><span class="inline-check"><span class="cb"></span> REVIEW BY GP IN ED - Date:&nbsp;</span></td>
                            <td style="width: 20%;"><span class="inline-check"><span class="cb"></span> DATE:&nbsp;</span></td>
                            <td style="width: 27%;"><span class="inline-check"><span class="cb"></span> TIME:&nbsp;</span></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container" rowspan="2">
                    <table class="border-none">
                        <tr>
                            <td style="width: 33%;"><span class="inline-check"><span class="cb"></span> REVIEW&nbsp;</span></td>
                            <td style="width: 33%;"><span class="inline-check"><span class="cb"></span> BY GP&nbsp;</span></td>
                            <td style="width: 34%;"><span class="inline-check"><span class="cb"></span> IN ED - Date&nbsp;</span></td>
                        </tr>
                        <tr>
                            <td style="width: 33%;"><span class="inline-check"><span class="cb"></span> CLINIC - Date&nbsp;</span></td>
                            <td style="width: 33%;"><span class="inline-check"><span class="cb"></span> OPD Date&nbsp;</span></td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr class="no-border">
                <td class="bold table-container" rowspan="2">
                    <table class="border-none">
                        <tr>
                            <td style="width: 50%;"><span class="inline-check"><span class="cb">{{ $checked('expired') }}</span> DIED IN ED Date&nbsp;{{ $outcomeValue === 'expired' ? $tr?->outcome_at?->format('d-m-Y') : '' }}</span></td>
                            <td style="width: 50%;"><span class="inline-check"><span class="cb"></span> Time&nbsp;{{ $outcomeValue === 'expired' ? $tr?->outcome_at?->format('H:i') : '' }}</span></td>
                        </tr>
                        <tr>
                            <td style="width: 50%;"><span class="inline-check"><span class="cb"></span> Left Against Medical Advice&nbsp;</span></td>
                            <td style="width: 50%;"><span class="inline-check"><span class="cb"></span> Did not want to be seen&nbsp;</span></td>
                        </tr>
                    </table>
                </td>
                <td>{{ $outcomeValue === 'expired' ? $tr?->outcome_notes : '' }}</td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td class="bold table-container">
                    <table class="border-none">
                        <tr>
                            <td style="width: 53%;"><span class="inline-check"><span class="cb"></span> DISCHARGING DOCTOR'S NAME:&nbsp;</span></td>
                            <td style="width: 23%;"><span class="inline-check"><span class="cb"></span> SIGNATURE:&nbsp;</span></td>
                        </tr>
                        <tr>
                            <td class="kv-line w-long">{{ $treatingDoctorName }}</td>
                            <td class="kv-line w-mid"></td>
                        </tr>
                    </table>
                </td>
                <td class="bold center">
                    <span class="inline-check"><span class="cb"></span> ISCHARGING NURSE / <br/> DISP. SIGN:&nbsp;</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
