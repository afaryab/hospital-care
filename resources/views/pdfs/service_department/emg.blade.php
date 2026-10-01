@use('App\Enum\TreatmentOutcome')
@use('App\Helpers\DateHelper')
@use('App\Helpers\PatientDisplay')
@use('App\Helpers\PrescriptionFormatter')
@php
    $hospitalName = \App\Models\HospitalSetting::name();
    $tr = $serviceOrder->treatmentRecord;
    $vital = $tr?->vitalSigns?->sortBy('recorded_at')->last();
    $extra = $tr?->department_specific_data ?? [];

    $doctor = $tr?->treatingDoctor ?? $serviceOrder->doctor;
    $doctorName = trim(($doctor?->name ?? '').($doctor?->pmdc_number ? " (PMDC# {$doctor->pmdc_number})" : ''));

    // Rows explicitly flagged, or (legacy rows) with an administration time,
    // were given in the ER; everything else is a discharge medicine.
    $prescriptions = collect($tr?->prescriptions ?? []);
    $givenInEr = fn (array $rx): bool => array_key_exists('given_in_er', $rx) ? (bool) $rx['given_in_er'] : filled($rx['given_at'] ?? null);
    $erMedicines = $prescriptions->filter($givenInEr)->values();
    $dischargeMedicines = $prescriptions->reject($givenInEr)->values();

    $notRecorded = '<span class="nr">Not recorded</span>';
    $show = fn ($value, string $suffix = '') => filled($value) ? e($value.$suffix) : $notRecorded;
    $number = fn ($value) => filled($value) ? rtrim(rtrim((string) $value, '0'), '.') : null;

    $bp = $vital && $vital->blood_pressure_systolic
        ? $vital->blood_pressure_systolic.'/'.($vital->blood_pressure_diastolic ?? '—')
        : null;
    $vitalTiles = [
        'BP' => [$bp, ' mmHg'],
        'Pulse' => [$vital?->pulse_rate, ' /min'],
        'Resp. rate' => [$vital?->respiratory_rate, ' /min'],
        'Temp' => [$number($vital?->temperature), ' °F'],
        'SpO₂' => [$number($vital?->oxygen_saturation), ' %'],
        'GCS' => [$vital?->gcs, ' /15'],
        'BSL' => [$number($vital?->blood_glucose), ' mg/dL'],
        'Weight' => [$number($vital?->weight), ' kg'],
    ];

    $pastHistory = [
        'HTN' => $patient?->history_htn,
        'DM' => $patient?->history_dm,
        'Asthma' => $patient?->history_asthma,
        'IHD' => $patient?->history_ihd,
    ];
    $yesNo = fn (?bool $value): string => match ($value) { true => 'Yes', false => 'No', default => '—' };

    // The EMG form records Airway and Breathing as examination-finding
    // entries; print what was entered rather than a Y/N template.
    $allFindings = collect($tr?->examination_findings ?? []);
    $airway = $allFindings->get('Airway');
    $breathing = $allFindings->get('Breathing');
    $examFindings = $allFindings->except(['Airway', 'Breathing'])->filter(fn ($value) => filled($value));

    $diagnosis = $tr?->icd10Code
        ? $tr->icd10Code->description.' ('.$tr->icd10Code->code.')'
        : $tr?->diagnosis_code;

    $outcome = $tr?->outcome;
    $tick = fn (TreatmentOutcome $case): string => $outcome === $case ? 'X' : '&nbsp;';
    $outcomeTime = $tr?->outcome_at ? DateHelper::pdfFormat($tr->outcome_at, 'd-m-Y H:i') : null;

    $prescribedAt = $tr?->treated_at ?? $tr?->created_at;
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Emergency Triage Note — {{ $patient?->ps_number }}</title>
    <style>
        @page { margin: 72px 18px 34px 18px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #000; font-size: 8.6px; line-height: 1.25; }

        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; padding: 2px 4px; }
        .grid td, .grid th { border: 0.6px solid #000; }
        .bar { background: #d9d9d9; font-weight: bold; font-size: 8.8px; letter-spacing: .4px; text-transform: uppercase; padding: 2px 4px; border: 0.6px solid #000; }
        .bar .meta { float: right; font-weight: normal; text-transform: none; letter-spacing: 0; }
        .label { font-weight: bold; white-space: nowrap; }
        .muted { color: #444; }
        .nr { color: #777; font-style: italic; }
        .gap { height: 5px; }
        .wrap { word-wrap: break-word; overflow-wrap: break-word; white-space: normal; }

        .title td { border: 0; padding: 0 0 4px 0; vertical-align: bottom; }
        .badge { display: inline-block; border: 1.2px solid #000; background: #d9d9d9; font-weight: bold; font-size: 11px; letter-spacing: 1px; padding: 3px 10px; }
        .ids { text-align: right; font-size: 9px; }
        .ids b { font-size: 10px; }

        .tiles td { border: 0.6px solid #000; width: 25%; text-align: center; padding: 3px 2px; }
        .tiles .t-label { font-size: 7.4px; text-transform: uppercase; letter-spacing: .3px; color: #333; }
        .tiles .t-value { font-size: 10px; font-weight: bold; }
        .tiles .t-value .nr { font-size: 7.6px; font-weight: normal; }

        .rx th { background: #efefef; font-size: 7.8px; text-transform: uppercase; text-align: left; }
        .rx td { font-size: 8.4px; }
        .legend { font-size: 7.6px; color: #333; padding: 2px 0 0 2px; }

        .cb { display: inline-block; width: 9px; height: 9px; border: 0.8px solid #000; text-align: center; line-height: 8px; font-size: 8px; font-weight: bold; margin-right: 3px; vertical-align: middle; }
        .dispo td { border: 0.6px solid #000; padding: 3px 4px; white-space: nowrap; }
        .line { border-bottom: 0.6px dotted #000; min-height: 12px; display: inline-block; }

        .sign td { border: 0; padding: 14px 6px 0 0; width: 25%; }
        .sign .rule { border-top: 0.6px solid #000; padding-top: 2px; font-size: 7.8px; }

    </style>
</head>
<body>
    @include('pdfs.partials.letterhead-header')
    @include('pdfs.partials.letterhead-footer')

    {{-- Title --}}
    <table class="title">
        <tr>
            <td style="width:50%;"><span class="badge">EMERGENCY · TRIAGE NOTE</span></td>
            <td class="ids">
                MR#: <b>{{ $patient?->ps_number ?? '—' }}</b> &nbsp;&nbsp;
                ER Visit#: <b>{{ $serviceOrder->so_short ?: $serviceOrder->so_number }}</b>
            </td>
        </tr>
    </table>

    {{-- Patient --}}
    <table class="grid">
        <tr>
            <td style="width:34%;"><span class="label">Name:</span> <span class="wrap">{{ $patient?->name }}</span></td>
            <td style="width:18%;"><span class="label">Age / Sex:</span> {!! filled(PatientDisplay::ageSex($patient)) ? e(PatientDisplay::ageSex($patient)) : $notRecorded !!}</td>
            <td style="width:28%;"><span class="label">S/o, D/o, W/o:</span> {{ trim(($patient?->relation ?? '').' '.($patient?->guardian ?? '')) ?: '—' }}</td>
            <td style="width:20%;"><span class="label">Triage:</span> {!! $show($tr?->triage?->name) !!}</td>
        </tr>
        <tr>
            <td><span class="label">Arrival:</span> {{ DateHelper::pdfFormat($serviceOrder->created_at, 'd-m-Y H:i') }}</td>
            <td><span class="label">Seen:</span> {!! $tr?->treated_at ? e(DateHelper::pdfFormat($tr->treated_at, 'H:i')) : $notRecorded !!}</td>
            <td colspan="2"><span class="label">ER Doctor:</span> {!! $show($doctorName) !!}</td>
        </tr>
    </table>

    <div class="gap"></div>

    {{-- Clinical: complaints + examination | vitals + past history --}}
    <table>
        <tr>
            <td style="width:55%; padding:0 3px 0 0;">
                <div class="bar">Presenting Complaints</div>
                <div class="wrap" style="border:0.6px solid #000; border-top:0; padding:3px 4px; min-height:40px;">
                    @if(filled($tr?->chief_complaint))<b>{{ $tr->chief_complaint }}</b><br>@endif
                    @if(filled($tr?->history_of_present_illness)){!! nl2br(e($tr->history_of_present_illness)) !!}@endif
                    @if(blank($tr?->chief_complaint) && blank($tr?->history_of_present_illness)){!! $notRecorded !!}@endif
                </div>

                <div class="bar" style="margin-top:4px;">General Examination</div>
                <table class="grid">
                    <tr>
                        <td style="width:50%;"><span class="label">Airway:</span> {!! $show($airway) !!}</td>
                        <td style="width:50%;"><span class="label">Breathing:</span> {!! $show($breathing) !!}</td>
                    </tr>
                    @forelse($examFindings->chunk(2) as $pair)
                        <tr>
                            @foreach($pair as $system => $finding)
                                <td class="wrap"><span class="label">{{ $system }}:</span> {{ $finding }}</td>
                            @endforeach
                            @if($pair->count() === 1)<td></td>@endif
                        </tr>
                    @empty
                        <tr><td colspan="2"><span class="label">Findings:</span> {!! $notRecorded !!}</td></tr>
                    @endforelse
                </table>
            </td>
            <td style="width:45%; padding:0 0 0 3px;">
                <div class="bar">Vitals @if($vital?->recorded_at)<span class="meta">{{ DateHelper::pdfFormat($vital->recorded_at, 'H:i') }}</span>@endif</div>
                <table class="tiles">
                    @foreach(array_chunk($vitalTiles, 4, true) as $row)
                        <tr>
                            @foreach($row as $label => [$value, $unit])
                                <td>
                                    <div class="t-label">{{ $label }}</div>
                                    <div class="t-value">{!! $show($value, $unit) !!}</div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>

                <div class="bar" style="margin-top:4px;">Past History</div>
                <table class="grid">
                    <tr>
                        @foreach($pastHistory as $condition => $value)
                            <td style="width:25%; text-align:center;"><span class="label">{{ $condition }}:</span> {{ $yesNo($value) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td colspan="4" class="wrap"><span class="label">Allergies:</span> {!! $show($patient?->allergies) !!}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="gap"></div>

    {{-- Treatment in ER | Investigations advised --}}
    <table>
        <tr>
            <td style="width:60%; padding:0 3px 0 0;">
                <div class="bar">Treatment Given in ER</div>
                <table class="grid rx">
                    <tr>
                        <th style="width:52%;">Drug</th>
                        <th style="width:26%;">Dose</th>
                        <th style="width:22%;">Route</th>
                    </tr>
                    @forelse($erMedicines as $rx)
                        <tr>
                            <td class="wrap">{{ $rx['drug_name'] ?? '' }}</td>
                            <td class="wrap">{{ $rx['dose'] ?? '' }}</td>
                            <td class="wrap">{{ $rx['route'] ?? '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">{!! $notRecorded !!}</td></tr>
                    @endforelse
                </table>
                @if(filled($tr?->treatment_plan))
                    <div class="wrap muted" style="padding:2px 2px 0;"><span class="label">Notes:</span> {{ $tr->treatment_plan }}</div>
                @endif
            </td>
            <td style="width:40%; padding:0 0 0 3px;">
                <div class="bar">Investigations Advised</div>
                <div class="wrap" style="border:0.6px solid #000; border-top:0; padding:3px 4px; min-height:30px;">
                    {!! filled($extra['investigations_advised'] ?? null) ? nl2br(e($extra['investigations_advised'])) : $notRecorded !!}
                </div>
            </td>
        </tr>
    </table>

    <div class="gap"></div>

    {{-- Discharge medicines --}}
    <div class="bar">
        Discharge Medicines
        @if($dischargeMedicines->isNotEmpty())
            <span class="meta">Prescribed {{ DateHelper::pdfFormat($prescribedAt, 'd-m-Y H:i') }} · {{ $doctorName }}</span>
        @endif
    </div>
    <table class="grid rx">
        <tr>
            <th style="width:4%;">#</th>
            <th style="width:31%;">Medicine</th>
            <th style="width:13%;">Qty / dose</th>
            <th style="width:17%;">Morning + Noon + Night</th>
            <th style="width:21%;">When to take</th>
            <th style="width:14%;">Duration</th>
        </tr>
        @forelse($dischargeMedicines as $i => $rx)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="wrap"><b>{{ $rx['drug_name'] ?? '' }}</b>@if(filled($rx['dose'] ?? null)) {{ $rx['dose'] }}@endif @if(filled($rx['instructions'] ?? null))<br><span class="muted">{{ $rx['instructions'] }}</span>@endif</td>
                <td class="wrap">{{ PrescriptionFormatter::quantity($rx) }}</td>
                <td class="wrap" style="text-align:center;"><b>{{ PrescriptionFormatter::pattern($rx['frequency'] ?? null) }}</b></td>
                <td class="wrap">{{ PrescriptionFormatter::when($rx['frequency'] ?? null) }}</td>
                <td class="wrap">{{ $rx['duration'] ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="6">{!! $notRecorded !!}</td></tr>
        @endforelse
    </table>
    <div class="legend">1 + 1 + 1 = Morning + Noon + Night</div>

    <div class="gap"></div>

    {{-- Diagnosis, advice, disposition --}}
    <table class="grid">
        <tr>
            <td style="width:50%;" class="wrap">
                <span class="label">Diagnosis:</span>
                {!! filled($diagnosis) ? e($diagnosis) : (filled($tr?->diagnosis_text) ? '' : $notRecorded) !!}
                @if(filled($tr?->diagnosis_text))<br>{{ $tr->diagnosis_text }}@endif
            </td>
            <td style="width:50%;" class="wrap">
                <span class="label">Advice / Follow-up:</span>
                {!! filled($extra['advice'] ?? null) ? nl2br(e($extra['advice'])) : (filled($tr?->follow_up_date) ? '' : $notRecorded) !!}
                @if(filled($tr?->follow_up_date))<br><span class="label">Follow-up on:</span> {{ DateHelper::pdfFormat($tr->follow_up_date, 'd-m-Y') }}@endif
            </td>
        </tr>
    </table>
    <table class="dispo" style="margin-top:3px;">
        <tr>
            <td style="width:20%;"><span class="cb">{!! $tick(TreatmentOutcome::Discharged) !!}</span>Discharged home</td>
            <td style="width:26%;"><span class="cb">{!! $tick(TreatmentOutcome::Referred) !!}</span>Referred / Transferred to: <span class="wrap">{{ $outcome === TreatmentOutcome::Referred ? $tr?->referral_to : '' }}</span></td>
            <td style="width:22%;"><span class="cb">{!! $tick(TreatmentOutcome::Admitted) !!}</span>Admitted to: {{ $outcome === TreatmentOutcome::Admitted ? ($extra['admitted_to'] ?? '') : '' }}</td>
            <td style="width:14%;"><span class="cb">{!! $tick(TreatmentOutcome::LeftAgainstMedicalAdvice) !!}</span>LAMA</td>
            <td style="width:18%;"><span class="cb">{!! $tick(TreatmentOutcome::Expired) !!}</span>Died in ER</td>
        </tr>
        @if($outcomeTime || filled($tr?->outcome_notes))
            <tr>
                <td colspan="5" class="wrap" style="white-space:normal;">
                    @if($outcomeTime)<span class="label">At:</span> {{ $outcomeTime }} &nbsp; @endif
                    {{ $tr?->outcome_notes }}
                </td>
            </tr>
        @endif
    </table>

    <div class="gap"></div>

    {{-- Consent + signatures --}}
    <div style="border:0.6px solid #000; padding:3px 4px; font-size:8px;">
        <span class="cb">&nbsp;</span>
        We give our consent to treatment according to the patient&rsquo;s condition. In case of any medication-related issue or loss of life, {{ $hospitalName }} will not be held responsible.
    </div>
    <table class="sign">
        <tr>
            <td><div class="rule"><b>Discharging Doctor</b><br>{{ $doctorName ?: ' ' }}</div></td>
            <td><div class="rule"><b>Patient / Attendant</b><br>Relation: ____________</div></td>
            <td><div class="rule"><b>Nurse</b><br>&nbsp;</div></td>
            <td><div class="rule"><b>Date / Time</b><br>&nbsp;</div></td>
        </tr>
    </table>
</body>
</html>
