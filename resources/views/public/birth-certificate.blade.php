<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Birth Certificate Verification</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 24px 16px;
        }
        .card {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .header {
            background: #0f172a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header .hospital { font-weight: 700; font-size: 16px; }
        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 12px;
            background: #16a34a;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .3px;
        }
        .body { padding: 20px; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; }
        .subtitle { color: #64748b; font-size: 13px; margin: 0 0 16px 0; }
        dl { margin: 0; }
        .row { display: flex; justify-content: space-between; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .row:last-child { border-bottom: 0; }
        dt { color: #64748b; font-size: 13px; }
        dd { margin: 0; font-weight: 600; font-size: 13px; text-align: right; }
        .footer { padding: 14px 20px; background: #f8fafc; font-size: 11px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    @php $genderLabels = ['m' => 'Male', 'f' => 'Female', 't' => 'Transgender', 'o' => 'Other']; @endphp
    <div class="card">
        <div class="header">
            <div class="hospital">{{ strtoupper(\App\Models\HospitalSetting::get('hospital_name', config('app.name'))) }}</div>
            <span class="badge">✓ Verified Official Record</span>
        </div>
        <div class="body">
            <p class="title">Birth Certificate</p>
            <p class="subtitle">Certificate No: {{ $certificate->birth_certificate_number }}</p>

            <dl>
                <div class="row"><dt>Child's Name</dt><dd>{{ $certificate->child_name ?: 'Baby of '.($certificate->mother_name ?? '') }}</dd></div>
                <div class="row"><dt>Gender</dt><dd>{{ $genderLabels[$certificate->gender] ?? $certificate->gender }}</dd></div>
                <div class="row"><dt>Date of Birth</dt><dd>{{ $certificate->date_of_birth?->format('d-m-Y') }}</dd></div>
                <div class="row"><dt>Time of Birth</dt><dd>{{ $certificate->time_of_birth ? \Illuminate\Support\Carbon::parse($certificate->time_of_birth)->format('H:i') : '' }}</dd></div>
                <div class="row"><dt>Mother's Name</dt><dd>{{ $certificate->mother_name }}</dd></div>
                <div class="row"><dt>Service Order</dt><dd>{{ $certificate->serviceOrder->so_number }}</dd></div>
            </dl>
        </div>
        <div class="footer">
            This page confirms the authenticity of an official record issued by {{ \App\Models\HospitalSetting::get('hospital_name', config('app.name')) }}.
        </div>
    </div>
</body>
</html>
