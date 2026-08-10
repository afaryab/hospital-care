@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $hospitalAddress = \App\Models\HospitalSetting::get('hospital_address');
    $hospitalPhone = \App\Models\HospitalSetting::get('hospital_phone');
    $tr = $serviceOrder->treatmentRecord;
    $referringDoctor = $tr?->treatingDoctor ?? $serviceOrder->doctor;
    $referringDoctorName = $referringDoctor?->name ?? '';
    if ($referringDoctor?->pmdc_number) {
        $referringDoctorName .= " (PMDC# {$referringDoctor->pmdc_number})";
    }
@endphp
<div class="page" style="page-break-before: always; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111;">
    <style>
        .rc-table{ width:100%; border-collapse:collapse; }
        .rc-table td, .rc-table th{ border:1px solid #222; padding:5px 7px; vertical-align:top; }
        .rc-label{ font-weight:700; background:#f0f0f0; white-space:nowrap; }
        .rc-title{ text-align:center; font-weight:800; font-size:15px; letter-spacing:.5px; margin: 4px 0 2px 0; text-decoration: underline; }
        .rc-sub{ text-align:center; font-size:11px; margin-bottom:8px; }
        .rc-head{ text-align:center; }
        .rc-head .name{ font-weight:800; font-size:14px; }
        .rc-notes{ border:1px solid #222; padding:8px; min-height:160px; margin-top:8px; }
        .rc-sig-box{ border:1px solid #222; padding:10px; height:60px; margin-top:4px; }
        .rc-stamp-box{ border:1px dashed #666; padding:10px; height:70px; text-align:center; color:#666; }
    </style>

    <div class="rc-head">
        <div class="name">{{ strtoupper($hospitalName) }}</div>
        @if($hospitalAddress)<div>{{ $hospitalAddress }}</div>@endif
        @if($hospitalPhone)<div>{{ $hospitalPhone }}</div>@endif
    </div>

    <div class="rc-title">REFERRAL CERTIFICATE</div>
    <div class="rc-sub">Referral No: <strong>{{ $referral->referral_number }}</strong> &nbsp;|&nbsp; Service Order: <strong>{{ $serviceOrder->so_number }}</strong></div>

    <table class="rc-table">
        <tr>
            <td class="rc-label" style="width:22%;">Patient Name</td>
            <td style="width:53%;">{{ $patient->name ?? '' }}</td>
            <td class="rc-label" style="width:10%;">Age / Sex</td>
            <td style="width:15%;">{{ $patient->age ?? '' }} / {{ $patient->gender ?? '' }}</td>
        </tr>
        <tr>
            <td class="rc-label">Address</td>
            <td colspan="3">{{ $patient->address ?? '' }}</td>
        </tr>
        <tr>
            <td class="rc-label">Referring Facility</td>
            <td>{{ $hospitalName }}</td>
            <td class="rc-label">Referring Doctor</td>
            <td>{{ $referringDoctorName }}</td>
        </tr>
        <tr>
            <td class="rc-label">Receiving Facility</td>
            <td colspan="3">{{ $referral->receiving_facility_name }}</td>
        </tr>
        <tr>
            <td class="rc-label">Diagnosis</td>
            <td colspan="3">{{ trim(($tr?->diagnosis_code ?? '').' '.($tr?->diagnosis_text ?? '')) }}</td>
        </tr>
    </table>

    <div class="rc-notes">
        {!! $referral->notes !!}
    </div>

    <table style="width:100%; margin-top:14px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="rc-sig-box">{{ $referringDoctorName }}</div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Referring Doctor</div>
            </td>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="rc-sig-box"></div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Receiving Doctor</div>
            </td>
            <td style="width:34%; vertical-align:top; border:0;">
                <div class="rc-stamp-box">Hospital Stamp</div>
            </td>
        </tr>
    </table>
</div>
