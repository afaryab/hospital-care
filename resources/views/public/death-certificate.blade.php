<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Death Certificate Verification</title>
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
    <div class="card">
        <div class="header">
            <div class="hospital">{{ strtoupper(\App\Models\HospitalSetting::get('hospital_name', config('app.name'))) }}</div>
            <span class="badge">✓ Verified Official Record</span>
        </div>
        <div class="body">
            <p class="title">Death Certificate</p>
            <p class="subtitle">Certificate No: {{ $certificate->certificate_number }}</p>

            <dl>
                <div class="row"><dt>Patient Name</dt><dd>{{ $patient->name ?? '' }}</dd></div>
                <div class="row"><dt>Date of Death</dt><dd>{{ $certificate->date_of_death?->format('d-m-Y') }}</dd></div>
                <div class="row"><dt>Time of Death</dt><dd>{{ $certificate->time_of_death ? \Illuminate\Support\Carbon::parse($certificate->time_of_death)->format('H:i') : '' }}</dd></div>
                <div class="row"><dt>Place of Death</dt><dd>{{ $certificate->place_of_death }}</dd></div>
                @if($certificate->manner_of_death)
                    <div class="row"><dt>Manner of Death</dt><dd>{{ $certificate->manner_of_death->label() }}</dd></div>
                @endif
                <div class="row"><dt>Service Order</dt><dd>{{ $certificate->serviceOrder->so_number }}</dd></div>
            </dl>
        </div>
        <div class="footer">
            This page confirms the authenticity of an official record issued by {{ \App\Models\HospitalSetting::get('hospital_name', config('app.name')) }}.
        </div>
    </div>
</body>
</html>
