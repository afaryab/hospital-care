@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $hospitalAddress = \App\Models\HospitalSetting::get('hospital_address');
    $hospitalPhone = \App\Models\HospitalSetting::get('hospital_phone');
    $tr = $serviceOrder->treatmentRecord;
    $treatingDoctor = $tr?->treatingDoctor ?? $serviceOrder->doctor;
    $treatingDoctorName = $treatingDoctor?->name ?? '';
    if ($treatingDoctor?->pmdc_number) {
        $treatingDoctorName .= " (PMDC# {$treatingDoctor->pmdc_number})";
    }
    $verificationUrl = \App\Helpers\QrCodeHelper::verificationUrl('v/dc/'.$certificate->verification_token);
    $qrDataUri = \App\Helpers\QrCodeHelper::dataUri($verificationUrl, 48);
@endphp
<div class="page" style="page-break-before: always; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111;">
    <style>
        .dc-table{ width:100%; border-collapse:collapse; }
        .dc-table td, .dc-table th{ border:1px solid #222; padding:5px 7px; vertical-align:top; }
        .dc-label{ font-weight:700; background:#f0f0f0; white-space:nowrap; }
        .dc-title{ text-align:center; font-weight:800; font-size:15px; letter-spacing:.5px; margin: 4px 0 2px 0; text-decoration: underline; }
        .dc-sub{ text-align:center; font-size:11px; margin-bottom:8px; }
        .dc-head{ text-align:center; }
        .dc-head .name{ font-weight:800; font-size:14px; }
        .dc-sig-box{ border:1px solid #222; padding:10px; height:60px; margin-top:4px; }
        .dc-stamp-box{ border:1px dashed #666; padding:10px; height:70px; text-align:center; color:#666; }
        .dc-qr{ width:48px; height:48px; }
        .dc-qr-caption{ font-size:7px; color:#666; text-align:center; width:60px; word-break:break-all; }
    </style>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:60px; border:0; text-align:center; vertical-align:top;">
                <img src="{{ $qrDataUri }}" class="dc-qr" alt="Verify">
                <div class="dc-qr-caption">Scan to verify</div>
            </td>
            <td class="dc-head" style="border:0;">
                <div class="name">{{ strtoupper($hospitalName) }}</div>
                @if($hospitalAddress)<div>{{ $hospitalAddress }}</div>@endif
                @if($hospitalPhone)<div>{{ $hospitalPhone }}</div>@endif
            </td>
            <td style="width:60px; border:0;"></td>
        </tr>
    </table>

    <div class="dc-title">MEDICAL CERTIFICATE OF CAUSE OF DEATH</div>
    <div class="dc-sub">Certificate No: <strong>{{ $certificate->certificate_number }}</strong> &nbsp;|&nbsp; Service Order: <strong>{{ $serviceOrder->so_number }}</strong></div>

    <table class="dc-table">
        <tr>
            <td class="dc-label" style="width:22%;">Patient Name</td>
            <td style="width:53%;">{{ $patient->name ?? '' }}</td>
            <td class="dc-label" style="width:10%;">Age / Sex</td>
            <td style="width:15%;">{{ $patient->age ?? '' }} / {{ $patient->gender ?? '' }}</td>
        </tr>
        <tr>
            <td class="dc-label">S/o, D/o, W/o</td>
            <td colspan="3">{{ trim(($patient->relation ?? '').' '.($patient->guardian ?? '')) }}</td>
        </tr>
        <tr>
            <td class="dc-label">Address</td>
            <td colspan="3">{{ $patient->address ?? '' }}</td>
        </tr>
        <tr>
            <td class="dc-label">Date of Death</td>
            <td>{{ $certificate->date_of_death?->format('d-m-Y') }}</td>
            <td class="dc-label">Time of Death</td>
            <td>{{ $certificate->time_of_death ? \Illuminate\Support\Carbon::parse($certificate->time_of_death)->format('H:i') : '' }}</td>
        </tr>
        <tr>
            <td class="dc-label">Place of Death</td>
            <td colspan="3">{{ $certificate->place_of_death }}</td>
        </tr>
        <tr>
            <td class="dc-label">Immediate Cause of Death</td>
            <td colspan="3">{{ trim(($tr?->diagnosis_code ?? '').' '.($tr?->diagnosis_text ?? '')) }}</td>
        </tr>
        <tr>
            <td class="dc-label">Antecedent Cause</td>
            <td colspan="3">{{ $certificate->antecedent_cause }}</td>
        </tr>
        <tr>
            <td class="dc-label">Manner of Death</td>
            <td colspan="3">{{ $certificate->manner_of_death?->label() }}</td>
        </tr>
        <tr>
            <td class="dc-label">Informant Name</td>
            <td>{{ $certificate->informant_name }}</td>
            <td class="dc-label">Relation</td>
            <td>{{ $certificate->informant_relation }}</td>
        </tr>
        <tr>
            <td class="dc-label">Informant CNIC</td>
            <td colspan="3">{{ $certificate->informant_cnic }}</td>
        </tr>
        <tr>
            <td class="dc-label">Remarks</td>
            <td colspan="3">{{ $certificate->remarks }}</td>
        </tr>
    </table>

    <table style="width:100%; margin-top:14px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="dc-sig-box">{{ $treatingDoctorName }}</div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Certifying Doctor</div>
            </td>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="dc-sig-box"></div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Medical Superintendent</div>
            </td>
            <td style="width:34%; vertical-align:top; border:0;">
                <div class="dc-stamp-box">Hospital Stamp</div>
            </td>
        </tr>
    </table>
</div>
