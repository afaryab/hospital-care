@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $hospitalAddress = \App\Models\HospitalSetting::get('hospital_address');
    $hospitalPhone = \App\Models\HospitalSetting::get('hospital_phone');
    $genderLabels = ['m' => 'Male', 'f' => 'Female', 't' => 'Transgender', 'o' => 'Other'];
    $doctorName = $certificate->attendingDoctor?->name ?? '';
    if ($certificate->attendingDoctor?->pmdc_number) {
        $doctorName .= " (PMDC# {$certificate->attendingDoctor->pmdc_number})";
    }
    $verificationUrl = \App\Helpers\QrCodeHelper::verificationUrl('v/bc/'.$certificate->verification_token);
    $qrDataUri = \App\Helpers\QrCodeHelper::dataUri($verificationUrl, 48);
@endphp
<div class="page" style="page-break-before: always; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111;">
    <style>
        .bc-table{ width:100%; border-collapse:collapse; }
        .bc-table td, .bc-table th{ border:1px solid #222; padding:5px 7px; vertical-align:top; }
        .bc-label{ font-weight:700; background:#f0f0f0; white-space:nowrap; }
        .bc-title{ text-align:center; font-weight:800; font-size:15px; letter-spacing:.5px; margin: 4px 0 2px 0; text-decoration: underline; }
        .bc-sub{ text-align:center; font-size:11px; margin-bottom:8px; }
        .bc-head{ text-align:center; }
        .bc-head .name{ font-weight:800; font-size:14px; }
        .bc-sig-box{ border:1px solid #222; padding:10px; height:60px; margin-top:4px; }
        .bc-stamp-box{ border:1px dashed #666; padding:10px; height:70px; text-align:center; color:#666; }
        .bc-qr{ width:48px; height:48px; }
        .bc-qr-caption{ font-size:7px; color:#666; text-align:center; width:60px; word-break:break-all; }
    </style>

    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:60px; border:0; text-align:center; vertical-align:top;">
                <img src="{{ $qrDataUri }}" class="bc-qr" alt="Verify">
                <div class="bc-qr-caption">Scan to verify</div>
            </td>
            <td class="bc-head" style="border:0;">
                <div class="name">{{ strtoupper($hospitalName) }}</div>
                @if($hospitalAddress)<div>{{ $hospitalAddress }}</div>@endif
                @if($hospitalPhone)<div>{{ $hospitalPhone }}</div>@endif
            </td>
            <td style="width:60px; border:0;"></td>
        </tr>
    </table>

    <div class="bc-title">BIRTH CERTIFICATE</div>
    <div class="bc-sub">Certificate No: <strong>{{ $certificate->birth_certificate_number }}</strong> &nbsp;|&nbsp; Service Order: <strong>{{ $serviceOrder->so_number }}</strong></div>

    <table class="bc-table">
        <tr>
            <td class="bc-label" style="width:22%;">Child's Name</td>
            <td style="width:53%;">{{ $certificate->child_name ?: 'Baby of '.($certificate->mother_name ?? $patient->name ?? '') }}</td>
            <td class="bc-label" style="width:10%;">Gender</td>
            <td style="width:15%;">{{ $genderLabels[$certificate->gender] ?? $certificate->gender }}</td>
        </tr>
        <tr>
            <td class="bc-label">Date of Birth</td>
            <td>{{ $certificate->date_of_birth?->format('d-m-Y') }}</td>
            <td class="bc-label">Time of Birth</td>
            <td>{{ $certificate->time_of_birth ? \Illuminate\Support\Carbon::parse($certificate->time_of_birth)->format('H:i') : '' }}</td>
        </tr>
        <tr>
            <td class="bc-label">Place of Birth</td>
            <td>{{ $certificate->place_of_birth ?: $hospitalName }}</td>
            <td class="bc-label">Weight at Birth</td>
            <td>{{ $certificate->weight_at_birth ? $certificate->weight_at_birth.' kg' : '' }}</td>
        </tr>
        <tr>
            <td class="bc-label">Mother's Name</td>
            <td>{{ $certificate->mother_name }}</td>
            <td class="bc-label">Mother's CNIC</td>
            <td>{{ $certificate->mother_cnic }}</td>
        </tr>
        <tr>
            <td class="bc-label">Father's Name</td>
            <td>{{ $certificate->father_name }}</td>
            <td class="bc-label">Father's CNIC</td>
            <td>{{ $certificate->father_cnic }}</td>
        </tr>
        <tr>
            <td class="bc-label">Remarks</td>
            <td colspan="3">{{ $certificate->remarks }}</td>
        </tr>
    </table>

    <table style="width:100%; margin-top:14px; border-collapse:collapse;">
        <tr>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="bc-sig-box">{{ $doctorName }}</div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Attending Doctor</div>
            </td>
            <td style="width:33%; vertical-align:top; padding-right:6px; border:0;">
                <div class="bc-sig-box"></div>
                <div style="text-align:center; font-weight:700; margin-top:2px;">Medical Superintendent</div>
            </td>
            <td style="width:34%; vertical-align:top; border:0;">
                <div class="bc-stamp-box">Hospital Stamp</div>
            </td>
        </tr>
    </table>
</div>
